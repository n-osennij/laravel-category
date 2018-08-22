<?php

namespace nosennij\LaravelCategory;

use Illuminate\View\View;
use nosennij\LaravelCategory\models\MyPackageCategory as Category;

class LaravelCategory
{
    private static $slug;
    private static $category;
    private static $categories;

    public function __construct(string $slug = null)
    {
        static::$slug = $slug;

        if (empty(static::$slug)) {
            static::$categories = Category::main()->get();
        } else {
            static::$category = self::slugCategory(static::$slug);
            static::$categories = static::$category->subcategories;
        }
    }

    /**
     * Возвращает готовое bootstrap 4.1 меню для переданного уровня.
     * Меню выводится по уровням (как папки в компьютере).
     * При выборе пункта меню загружаются все подкатегории одним уровнем ниже.
     *
     * @return View
     */
    public static function createCategoryMenu(): View
    {
        return view('nosennij::category_menu', [
            'category' => static::$category,
            'categories' => static::$categories,
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
     * @return View
     */
    public static function createCategoryBreadcrumbs(string $append = null): View
    {
        if (!empty(static::$slug)) {
            $category = static::$category;
            $breadcrumbs = array($category->toArray());
            $i = 15; //ограничитель глубины циклов. На случай ошибки в цепочке категорий.
            while ($parent = $category->parent) {
                if ($i <= 0) break;
                array_push($breadcrumbs, $parent->toArray());
                $category = $parent;
                $i--;
            }
            krsort($breadcrumbs);
        }

        return view('nosennij::category_breadcrumbs', compact('breadcrumbs', 'append'));
    }

    /**
     * Возвращает готовые bootstrap 4.1 карточки (card) категорий с названием категории по центру.
     * Работает по аналогии с createCategoryMenu(), только тут выводятся карточки.
     *
     * @return View
     */
    public static function createCategoryCards(): View
    {
        return view('nosennij::category_card', [
            'categories' => static::$categories,
        ]);
    }

    /**
     * Возвращает дерево всех категорий в виде массива.
     *
     * @return array
     */
    public static function getCategoryTree(): array
    {
        return static::buildTree(Category::all()->toArray());
    }

    /**
     * Ищет в БД по $slug категорию.
     * Если категории нет, вернёт 404.
     *
     * @param string $slug
     * @return Category
     */
    private static function slugCategory(string $slug): Category
    {
        return Category::where('slug', $slug)->firstOrFail();
    }

    /**
     * На основе всех категорий строит многомерный дерево-массив категорий.
     *
     * @param array $items
     * @return array
     */
    private static function buildTree(array $items): array
    {
        $childs = array();
        foreach ($items as &$item) {
            $childs[$item['parent_id']][] = &$item;
        }
        unset($item);
        foreach ($items as &$item) {
            if (isset($childs[$item['id']])) {
                $item['childs'] = $childs[$item['id']];
            }
        }

        return $childs[0];
    }
}
