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
     * Если $slug пуст (когда находится на самом верху - на главной), вернёт пустой вид
     *
     * @return View
     */
    public static function createCategoryBreadcrumbs(): View
    {
        if (!empty(static::$slug)) {
            $category = static::$category;
            $breadcrumbs = array($category->toArray());
            while ($parent = $category->parent) {
                array_push($breadcrumbs, $parent->toArray());
                $category = $parent;
            }
            krsort($breadcrumbs);
        }

        return view('nosennij::category_breadcrumbs', compact('breadcrumbs'));
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
     * Ищет в БД по $slug категорию.
     * Если категории нет, вернёт 404
     *
     * @param string $slug
     * @return Category
     */
    private static function slugCategory(string $slug): Category
    {
        return Category::where('slug', $slug)->firstOrFail();
    }
}