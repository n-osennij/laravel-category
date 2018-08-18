<?php

use Faker\Generator as Faker;
use nosennij\LaravelCategory\models\MyPackageCategory as Category;

$factory->define(Category::class, function (Faker $faker) {

    $slug = $faker->unique()->slug;
    $name = implode(' ', explode('-', $slug));

    $image = $faker->image($dir = 'public\storage', $width = 300, $height = 300);
    $path = explode('\\', $image);
    $path = $path[count($path) - 1];

    return [
        'parent_id' => rand(0, 50),
        'name' => $name,
        'slug' => $slug,
        'img' => $path,
    ];
});
