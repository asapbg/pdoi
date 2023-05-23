<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('permissions') as $name => $display_name) {
            $record = Permission::where('name', $name)->first();

            if ($record) {
                $this->command->line("Permission $display_name already exists in db");
                continue;
            }

            Permission::create([
                'name' => $name,
                'display_name' => $display_name
            ]);

            $this->command->info("Permission with name $display_name created successfully");
        }

        $role = Role::whereName('admin')->first();
        $role->givePermissionTo('manage.*');

        //add permissions to our role
        $role = Role::whereName('super_admin')->first();
        $role->givePermissionTo('manage.*');
    }
}
