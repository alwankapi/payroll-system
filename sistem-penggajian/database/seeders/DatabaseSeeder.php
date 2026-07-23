<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            JabatanSeeder::class,
            PotonganSeeder::class,
            UserSeeder::class,
            KaryawanSeeder::class,
        ]);
    }
}