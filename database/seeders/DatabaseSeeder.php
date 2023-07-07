<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RolesSeeder::class,
            PermissionsSeeder::class,
            UsersSeeder::class,
            RzsSectionSeeder::class,
            CategorySeeder::class,
            ExtendTermsReasonSeeder::class,
            ReasonRefusalSeeder::class,
            EventSeeder::class
        ]);
    }
}
