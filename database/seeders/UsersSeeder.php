<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // make asap user with admin role
        $user = new User;
        $user->username = "super_admin";
        $user->password = bcrypt('pass123');
        $user->user_type = User::USER_TYPE_INTERNAL;
        $user->names = 'Asap SuperAdmin';
        $user->email = 'super_admin@asap.bg';
        $user->status = User::STATUS_ACTIVE;
        $user->pass_last_change = Carbon::now();
        $user->pass_is_new = 1;
        $user->save();

        $this->command->info("User with email: $user->email saved");

        $role = Role::where('name', 'super_admin')->first();
        $user->assignRole($role);

        $this->command->info("Role $role->name was assigned to $user->first_name $user->last_name");
    }
}
