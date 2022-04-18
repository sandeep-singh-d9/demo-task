<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = ['admin', 'manager', 'client'];
        foreach ($data as $key => $value) {
            Role::create(
                ['name' => $value]
            );
        }
    }
}
