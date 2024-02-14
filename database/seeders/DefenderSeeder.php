<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefenderSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
        ]);
    }
}
