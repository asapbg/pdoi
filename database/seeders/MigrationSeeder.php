<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            CategorySeeder::class,
            ApplicationSeeder::class,
            ApplicationEventSeeder::class,
            FileSeeder::class,
        ]);
    }
}
