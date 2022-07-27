<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'id' => "1",
            'slug' => "first",
            'name' => 'Первая категория',
            'description' => 'Описание категории first',
            'createdDate' => '2022-07-18 10:55:40',
            'active' => 1
        ]);
        DB::table('categories')->insert([
            'id' => "2",
            'slug' => "second",
            'name' => 'Мёд',
            'description' => 'Описание второй категории',
            'createdDate' => '2022-07-18 10:56:17',
            'active' => 0
        ]);
        DB::table('categories')->insert([
            'id' => "3",
            'slug' => "three",
            'name' => 'Новая-_категория',
            'description' => 'Desc-_Описание',
            'createdDate' => '2022-07-20 16:26:28',
            'active' => 1
        ]);
        DB::table('categories')->insert([
            'id' => "4",
            'slug' => "four",
            'name' => 'qwerty',
            'description' => 'is-awesome',
            'createdDate' => '2022-07-21 10:45:26',
            'active' => 1
        ]);
    }
}
