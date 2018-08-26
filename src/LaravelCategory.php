<?php

namespace nosennij\LaravelCategory;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use nosennij\LaravelCategory\models\Category;

class LaravelCategory
{
    /**
     * Данные категории из БД
     *
     * @var Category
     */
    private $category;

    /**
     * Коллекция категорий из БД. Или текущего уровня(если нет slug). Или уровня ниже
     *
     * @var Collection
     */
    private $categories;

    /**
     * Время хренения кеша в минутах, если этот кеш создаётся
     * Свойство сделано public, чтобы можно было легко его изменить
     *
     * @var int
     */
    public $cache_time = 5;

    /**
     * Готовит стартовую информацию для работы класса, используя $slug
     *
     * @param string|null $slug
     * @return LaravelCategory
     */
    public function initWithSlug(string $slug = null): LaravelCategory
    {
        if (!empty($slug)) {
            $this->category = self::slugCategory($slug);
        }
        $this->init($slug);

        return $this;
    }

    /**
     * Заполняет категорию класса переданным значением и готовит остальные данные.
     *
     * @param $category
     * @return LaravelCategory
     */
    public function initWithCategory($category): LaravelCategory
    {
        $slug = $category->slug ?? null;
        if (!empty($slug)) {
            $this->category = $category;
        }
        $this->init($slug);

        return $this;
    }

    /**
     * На основе $slug устанавливает категории главного уровня или уровнем ниже
     *
     * @param string|null $slug
     */
    private function init(string $slug = null)
    {
        if (!empty($slug)) {
            $this->categories = $this->category->subcategories;
        } else {
            $this->categories = Category::main()->get();
        }
    }

    /**
     * Возвращает готовое bootstrap 4.1 меню для переданного уровня.
     * Меню выводится по уровням (как папки в компьютере).
     * При выборе пункта меню загружаются все подкатегории одним уровнем ниже.
     *
     * @return View
     */
    public function createCategoryMenu(): View
    {
        return view('nosennij::category_menu', [
            'category' => $this->category,
            'categories' => $this->categories,
        ]);
    }

    /**
     * Возвращает готовые bootstrap 4.1 хлебные крошки.
     * Ищет текущую категорию по $slug, а затем все котеории выше.
     * Для удобного вывода сортирует полученный массив категория в обратном порядке по ключу.
     * Если $slug пуст (когда находится на самом верху - на главной), вернёт пустой вид.
     *
     * @param string|null $append - Для добавления последней хлебной крошки, которая не связана с категориями.
     * Например, для отображения хлебных крошек на старнице товара удобно вывести все ссылки активыными,
     * а последнюю (имя товара) - нет. Т.к. класс работает только с категориями, то передать имя товара -
     * последней хлебной крошки нужно отдельно.
     *
     * @return string
     */
    public function createCategoryBreadcrumbs(string $append = null): string
    {
        $slug = $this->category->slug ?? null;
        $name = 'breadcrumbs_' . $slug . '_' . implode('-', explode(' ', $append));

        $value = Cache::remember($name, $this->cache_time, function () use ($slug, $append) {

            if (!empty($slug)) $breadcrumbs = $this->buildBreadcrumbs($slug, $append);
            $view = view('nosennij::category_breadcrumbs', compact('breadcrumbs', 'append'));

            return $view->render();

        });

        return $value;
    }

    /**
     * Готовит массив хлебных крошек
     *
     * @param string $slug
     * @param string|null $append
     * @return array
     */
    private function buildBreadcrumbs(string $slug, string $append = null)
    {
        $category = $this->category;
        $breadcrumbs = array($category->toArray());
        $i = 15; //ограничитель глубины циклов. На случай ошибки в цепочке категорий.
        while ($parent = $category->parent) {
            if ($i <= 0) break;
            array_push($breadcrumbs, $parent->toArray());
            $category = $parent;
            $i--;
        }
        krsort($breadcrumbs);

        //добавляем поселюднюю хлебную крошку
        if (!empty($append)) {
            $breadcrumbs[] = [
                'name' => $append,
                'slug' => '',
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Возвращает готовые bootstrap 4.1 карточки (card) категорий с названием категории по центру.
     * Работает по аналогии с createCategoryMenu(), только тут выводятся карточки.
     *
     * @return View
     */
    public function createCategoryCards(): View
    {
        return view('nosennij::category_card', [
            'categories' => $this->categories,
        ]);
    }

    /**
     * Возвращает дерево всех категорий в виде массива.
     *
     * @return array
     */
    public function getCategoryTree(): array
    {
        return Cache::remember('category_tree', $this->cache_time, function () {
            return $this->buildTree(Category::all()->toArray());
        });
    }

    /**
     * Ищет в БД по $slug категорию.
     * Если категории нет, вернёт 404.
     *
     * @param string $slug
     * @return Category
     */
    private function slugCategory(string $slug): Category
    {
        return Category::where('slug', $slug)->firstOrFail();
    }

    /**
     * На основе всех категорий строит многомерный дерево-массив категорий.
     *
     * @param array $items
     * @return array
     */
    private function buildTree(array $items): array
    {
        $children = array();
        foreach ($items as &$item) {
            $children[$item['parent_id']][] = &$item;
        }
        unset($item);
        foreach ($items as &$item) {
            if (isset($children[$item['id']])) {
                $item['children'] = $children[$item['id']];
            }
        }

        return $children[0];
    }
}
