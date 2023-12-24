<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\OptionGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('options')->delete();
        $faker = \Faker\Factory::create();
        $optionGroups = OptionGroup::all()->pluck('id');

        $total = rand(3, 9);
        for ($i = 0; $i < $total; $i++) {
            $model = new Option();
            $model->name_ar = $faker->company;
            $model->name_en = $faker->company;
            $model->description = $faker->text;
            $model->price = $faker->randomNumber(3);
            $model->option_group_id = $i + 1;
            $model->is_active = 1;
            $model->save();
        }
    }
}
