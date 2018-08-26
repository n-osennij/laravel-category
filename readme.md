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
       $ composer require n.osennij/laravel-category
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
            transition: all 0.25s ease-in;
        }
        .laravelcategorycard a:hover > .card-img {
            opacity: 0.5;
            -webkit-box-shadow: 0 0.8rem 3rem rgba(0, 0, 0, 0.075) !important;
            box-shadow: 0 0.8rem 3rem rgba(0, 0, 0, 0.075) !important;
        }
        ``` 
   1. И в конце ваша модель для таблицы категорий (если есть такая) должна наследоваться от модели категорий пакета
   
   ```
   <?php
   
   namespace App;
   
   use nosennij\LaravelCategory\models\Category as LaravelCategory;
   
   class Category extends LaravelCategory
   {
      // Тут ваши личные методы
   }
   ```       
        
### Дополнительно
В ходе миграфии базы данных создаётся таблица `categories` следующей структуры

```
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(115) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_id_parent_id_index` (`id`,`parent_id`),
  KEY `categories_parent_id_id_index` (`parent_id`,`id`),
  KEY `categories_parent_id_index` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

Для работы с изображениями следует создать симольную ссылку на хранилище

```
php artisan storage:link
```

Заполнить таблицу тестовыми данными можно следующей командой (если импортировали сида и фабрику), предварительно зарегестрировав сид в файле `database/seeds/CategoriesTableSeeder.php` - `$this->call(CategoriesTableSeeder::class);` и выполнив `composer dumpautoload`

```
composer dumpautoload
php artisan db:seed
```


##Использование

Сначала создадим роут
```
Route::get('/category/{slug?}', 'CategoryController@index')->name('category');
```
- slug - название категории c `-` вместо пробелов (см БД)
- category - название роута

При необходимости можно изменить их названия в настройках. Это нужно для правильной генерации ссылок в меню категорий с использованием функции `route()`.

Далее в контроллере подключите пакет и создаёте экземляр класса.
После этого есть два вариант:
- Передать `string $slug` - `$lc->initWithSlug($slug)`
- Передать `Category $category` - `$lc->initWithCategory($category)`

```
<?php

namespace App\Http\Controllers;

use nosennij\LaravelCategory\LaravelCategory; //подключаем пакет
use Illuminate\Http\Request;

class CategoryController extends Controller
{
   public function index(Request $request)
   {
       $lc = new LaravelCategory(); //Созадём экземляр класса
       $lc->initWithSlug($request->$slug);
       
       //удобнее и лучше этот вариант, 
       //т.к. обычно в самом методе мы уже получаем категорию из роута
       //$lc->initWithCategory($category);
      
       return view('catalog', [
           'categories_numu' => $lc->createCategoryMenu(),
           'breadcrumb' => $lc->createCategoryBreadcrumbs(),
           'categories_сards' => $lc->createCategoryCards(),
       ]);
    }
}
```

| Метод  | Параметры | Вернёт | Описание |  Кеш |
| ------------- | ------------- | ------------- | ------------- | ------------- |
| initWithSlug()  |   | $this | Инициализирует класс по $slug категории |  |
| initWithCategory()  |   | $this | Инициализирует класс по объекту модели категории  | |
| createCategoryMenu()  |   | html | Создаёт bootstrap 4 пошаговое меню категорий  | |
| createCategoryBreadcrumbs()  | `string $append`  | html или null | Создаёт bootstrap 4 хлебные крошки | да |
| createCategoryCards()  |   | html или null | Создаёт bootstrap 4 карточки категорий  | |
| getCategoryTree()  |   | array | Строит дерево всех категори и возвращает в виде многомерного массива  | да |

`string $append` - Для добавления последней хлебной крошки, которая не связана с категориями. Например, для отображения хлебных крошек на старнице товара удобно вывести все ссылки активыными, а последнюю (имя товара) - нет. Т.к. класс работает только с категориями, то передать имя товара - последней хлебной крошки нужно отдельно.

Кеширование данных по-умолчанию - 5 минут. Для метода `createCategoryBreadcrumbs()` кешируется готовый html для каждого url, чтобы не делать одно и то же при обновлении страницы и хождении туда-обратно. Для метода `getCategoryTree()` кешируется готовых массив всех категорий.

Изменить время (в минутых) хранения кеша можно так:

```
$lc = new LaravelCategory();
$lc->cache_time = 1; //Храним одну минуту
$lc->cache_time = 0; //Отключаем кеш
//Если кешировали, а потом отключили, то старый кеш останется. Его нужно очистить или подождать.
```

Для вывода на странице html используйте в шаблоне `{!! $your_data !!}`


[ico-version]: https://img.shields.io/packagist/v/nosennij/laravelcategory.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/nosennij/laravelcategory.svg?style=flat-square
[ico-travis]: https://travis-ci.org/n-osennij/laravel-category.svg?branch=master
[ico-styleci]: https://styleci.io/repos/145241800/shield

[link-packagist]: https://packagist.org/packages/n.osennij/laravel-category
[link-downloads]: https://packagist.org/packages/n.osennij/laravel-category
[link-travis]: https://travis-ci.org/n-osennij/laravel-category
[link-styleci]: https://styleci.io/repos/145241800
