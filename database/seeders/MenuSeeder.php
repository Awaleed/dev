<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->delete();
        $faker = \Faker\Factory::create();

        $vendors = Vendor::all()->pluck('id');

        foreach ($vendors as $vendor) {
            for ($i = 0; $i < 5; $i++) {
                $model = new Menu();
                $model->name_ar = $faker->company;
                $model->name_en = $faker->company;
                $model->is_active = 1;
                $model->vendor_id = $vendor;
                $model->save();
            }
        }
    }
}
