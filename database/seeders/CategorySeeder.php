<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        // DB::table('categories')->delete();

        for ($i = 0; $i < 20; $i++) {
            $model = new Category();
            $model->name_en = $faker->company;
            $model->name_ar = $faker->company;
            $model->save();
            $model->addMediaFromUrl("https://source.unsplash.com/512x512/?food")->toMediaCollection("logo");
        }
    }
}
