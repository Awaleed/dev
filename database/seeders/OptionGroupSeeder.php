<?php

namespace Database\Seeders;

use App\Models\OptionGroup;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('option_groups')->delete();
        $faker = \Faker\Factory::create();
        $vendors = Vendor::all()->pluck('id');
        $totalVendors = rand(3, 9);

        foreach ($vendors as $vendor) {
            for ($i = 0; $i < $totalVendors; $i++) {
                $model = new OptionGroup();
                $model->name_ar = $faker->company;
                $model->name_en = $faker->company;
                $model->vendor_id = $vendor;
                $model->is_active = 1;
                $model->save();
            }
        }
    }
}
