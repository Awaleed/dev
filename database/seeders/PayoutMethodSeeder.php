<?php

namespace Database\Seeders;

use App\Models\PayoutMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PayoutMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payout_methods')->delete();

        DB::table('payout_methods')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name_ar' => 'استلام يدوي',
                'name_en' => 'Manual pickup',
                'slug' => 'manual-pickup',
                'is_active' => 1,
                'created_at' => '2021-01-09 12:38:10',
                'updated_at' => '2021-07-17 10:49:00',
            ),
            1 =>
            array(
                'id' => 2,
                'name_ar' => 'حوالة بنكية',
                'name_en' => 'Bank transfer',
                'slug' => 'bank-transfer',
                'is_active' => 1,
                'created_at' => '2021-01-09 12:38:10',
                'updated_at' => '2021-07-17 10:49:11',
            ),
            2 =>
            array(
                'id' => 3,
                'name_ar' => 'لم يتم التحويل',
                'name_en' => 'NAN',
                'slug' => 'nan',
                'is_active' => 1,
                'created_at' => '2021-01-09 12:38:10',
                'updated_at' => '2021-07-17 10:49:11',
            ),
        ));
    }
}
