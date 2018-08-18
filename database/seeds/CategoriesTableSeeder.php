<?php

use Illuminate\Database\Seeder;
use nosennij\LaravelCategory\models\MyPackageCategory as Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Category::class, 100)->create();
    }
}
