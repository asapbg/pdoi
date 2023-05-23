<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('roles');

        foreach ($roles as $name => $display_name) {
            if (!Role::where('name', $name)->first()) {
                Role::create([
                    'name' => $name,
                    'display_name' => $display_name
                ]);
                $this->command->info("Role: " . $name . " save in db");

            } else {
                $this->command->comment("Role: $display_name | $name already exist in DB");
            }
        }
    }
}
