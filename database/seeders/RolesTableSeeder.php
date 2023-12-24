<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Role::create(['guard_name' => 'api', 'name' => 'manager']);
        Role::create(['guard_name' => 'api', 'name' => 'client']);
        Role::create(['guard_name' => 'api', 'name' => 'driver']);
        Role::create(['guard_name' => 'api', 'name' => 'employee']);
    }
}
