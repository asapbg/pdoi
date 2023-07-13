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
            ApplicationSeeder::class,
            ApplicationEventSeeder::class,
            //TODO save application files
            //TODO save application events files
        ]);
    }
}
