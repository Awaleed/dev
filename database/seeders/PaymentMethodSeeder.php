<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->delete();

        DB::table('payment_methods')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name_en' => 'Cash On Delivery',
                'name_ar' => 'الدفع عند الاستلام',
                'slug' => 'cash',
                'instruction' => 'This is the method of payment upon receipt',
                'secret_key' => null,
                'public_key' => null,
                'hash_key' => null,
                'class' => null,
                'is_active' => 1,
                'is_cash' => 1,
                'created_at' => '2021-01-09 12:38:10',
                'updated_at' => '2021-07-17 10:49:00',
                'deleted_at' => null,
            ),
            1 =>
            array(
                'id' => 2,
                'name_en' => 'Wallet Balance',
                'name_ar' => 'رصيد المحقظة',
                'slug' => 'wallet',
                'instruction' => null,
                'secret_key' => 'sk-TP7aEW4BeWe5wpCnML6Wwk69Kb0sp2FchliJy3Ml9yA',
                'public_key' => 'pk-E4ZPv8YvDsnoIbq7iLw8c5BcLdUDhglVZTI20Oa4cwX',
                'hash_key' => null,
                'class' => null,
                'is_active' => 1,
                'is_cash' => 1,
                'created_at' => '2021-01-09 12:38:10',
                'updated_at' => '2021-07-17 10:49:11',
                'deleted_at' => null,
            ),
            2 =>
            array(
                'id' => 3,
                'name_en' => 'Mada card',
                'name_ar' => 'بطاقة مدى',
                'slug' => 'moyaser',
                'instruction' => null,
                'secret_key' => '',
                'public_key' => '',
                'hash_key' => null,
                'class' => null,
                'is_active' => 1,
                'is_cash' => 1,
                'created_at' => '2021-01-09 12:38:10',
                'updated_at' => '2021-07-17 10:49:11',
                'deleted_at' => null,
            ),
        ));
    }
}
