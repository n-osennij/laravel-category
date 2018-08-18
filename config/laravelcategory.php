<?php

return [
    'main_level' => 'Главная', //Название первого уровня категорий. Т.е. название раздела с категориями
    'route' => [ //данные для генерации ссылки. Пример роута Route::get('/category/{slug?}', 'CategoryController@index')->name('category');
        'name' => 'category', // название роута ->name()
        'params' => [ //параметры роута
            'slug' => 'slug' // как в роуте называется параметр в который передаётся slug категории
        ],
    ],
    'storage' => '/storage/', //путь до папки с изображенийми для категорий
    'empty_category_image' => 'https://c1.staticflickr.com/5/4034/4544827697_6f73866999_n.jpg', //путь до пустой картинки категорий, которые не имеют собственной
];