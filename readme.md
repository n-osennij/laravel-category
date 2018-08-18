# LaravelCategory
   
   [![Latest Version on Packagist][ico-version]][link-packagist]
   [![Total Downloads][ico-downloads]][link-downloads]
   [![Build Status][ico-travis]][link-travis]
   [![StyleCI][ico-styleci]][link-styleci]
   
   Пакет добавляет возможность быстро и удобно создавать:
   - ступенчатое меню категорий
   - "хлебные крошки"
   - карточки категорий с изображениями 
   
   ![](https://media.giphy.com/media/1zJnGQZKXnC2XIK6oc/giphy.gif)
   
## Зависимости
   - Laravel 5.6
   - Bootstrap 4.1
   
## Установка
### Основное
   
   1. Установите laravel и настройте подключение к БД в файле `.env`
   1. Установите пакет через Composer
       ``` bash
       $ composer require nosennij/laravel-category
       ```
   1. Выполните миграцию базы данных командой
       ```
       php artisan migrate
       ```    
   1. Можно переместить в дирректории проекта файлы пакета. Для этого выполните команду
       ```
       $ php artisan vendor:publish --force
       ```
       Обязательным условием работы является импорт файла настроек - опция `laravelcategory.config`!
   
       | Опция  | Куда перемещает файл | Описание |
       | ------------- | ------------- | ------------- |
       | nosennij\LaravelCategory\LaravelCategoryServiceProvider | см. ниже  | Перемещает все заданные файлы за раз
       | laravelcategory.db  | \database\  | Перемещает миграфию, а также сида и фабрику для генерации тестовых данных |
       | laravelcategory.views  | \resources\views\vendor\nosennij  | Перемещает виды для возможности их редактирования |
       | laravelcategory.config  | \config  | Перемещает файл настроек |
   1. Добавте css стили (необходимы для корректного вывода карточек категорий)
        ```
        .laravelcategorycard .text {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          padding: 10px;
          opacity: 1;
        }
        .laravelcategorycard a > .card-img {
          opacity: 0.2;
        }
        .laravelcategorycard a:hover > .card-img {
          opacity: 0.5;
        }
        ```        
        
### Дополнительно
В ходе миграфии базы данных создаётся таблица `categories` следующей структуры

```
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(115) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_id_parent_id_index` (`id`,`parent_id`),
  KEY `categories_parent_id_id_index` (`parent_id`,`id`),
  KEY `categories_parent_id_index` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

Заполнить таблицу тестовыми данными можно следующей командой (если импортировали сида и фабрику)

```
`php artisan db:seed`
```

##Использование

Сначала создадим роут
```
Route::get('/category/{slug?}', 'CategoryController@index')->name('category');
```
- slug - название категории c `-` вместо пробелов (см БД)
- category - название роута

При необходимости можно изменить их названия в настройках. Это нужно для правильной генерации ссылок в меню категорий с использованием функции `route()`.

Далее в контроллере подключите пакет и создаёте экземляр класса, передавая ему в качестве параметра (опционально) $slug.

```
<?php

namespace App\Http\Controllers;

use nosennij\LaravelCategory\LaravelCategory; //подключаем пакет
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    $lc = new LaravelCategory($request->slug); //Созадём экземляр класса
    
    return view('catalog', [
        'categories_numu' => $lc::createCategoryMenu(),
        'breadcrumb' => $lc::createCategoryBreadcrumbs(),
        'categories_сards' => $lc::createCategoryCards(),
    ]);
}
```

| Метод  | Параметры | Вернёт | Описание |
| ------------- | ------------- | ------------- | ------------- |
| ::createCategoryMenu()  |   | html | Создаёт bootstrap 4 пошаговое меню категорий
| ::createCategoryBreadcrumbs()  |   | html или null | Создаёт bootstrap 4 хлебные крошки
| ::createCategoryCards()  |   | html или null | Создаёт bootstrap 4 карточки категорий

Для вывода на странице html используйте в шаблоне `{!! $your_data !!}`


[ico-version]: https://img.shields.io/packagist/v/nosennij/laravelcategory.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/nosennij/laravelcategory.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/nosennij/laravelcategory/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/145241800/shield

[link-packagist]: https://packagist.org/packages/nosennij/laravelcategory
[link-downloads]: https://packagist.org/packages/nosennij/laravelcategory
[link-travis]: https://travis-ci.org/nosennij/laravelcategory
[link-styleci]: https://styleci.io/repos/145241800
