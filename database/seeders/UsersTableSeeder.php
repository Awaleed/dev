<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        User::create([
            'name' => 'Admin Account',
            'email' => 'admin@demo.com',
            'password' => 'password',
        ]);

        // $review = new Review(['body' => 'review text', 'user_id' => 5]);
        // User::find(1)->reviews()->save($review);
    }
}
