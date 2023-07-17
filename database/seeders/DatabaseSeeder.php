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
            SettingsSeeder::class,
            CountrySeeder::class,
//            UsersSeeder::class, // we insert our users after old user migration
            RzsSectionSeeder::class,
            CategorySeeder::class,
            ExtendTermsReasonSeeder::class,
            ReasonRefusalSeeder::class,
            EventSeeder::class,
            MailTemplateSeeder::class,
            MigrationSeeder:: class
        ]);
    }
}
