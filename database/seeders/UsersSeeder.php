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
        // make asap user with super user role
        $user = new User;
        $user->username = "service_user";
        $user->password = bcrypt('pass123');
        $user->user_type = User::USER_TYPE_INTERNAL;
        $user->names = 'Сервизен потребител';
        $user->email = 'service_user@asap.bg';
        $user->status = User::STATUS_ACTIVE;
        $user->pass_last_change = Carbon::now();
        $user->pass_is_new = 1;
        $user->save();

        $this->command->info("User with email: $user->email saved");

        $role = Role::where('name', 'service_user')->first();
        $user->assignRole($role);

        $this->command->info("Role $role->name was assigned to $user->names");

        // make asap user with super user role
        $user = new User;
        $user->username = "admin";
        $user->password = bcrypt('pass123');
        $user->user_type = User::USER_TYPE_INTERNAL;
        $user->names = 'Админситратор';
        $user->email = 'admin@asap.bg';
        $user->status = User::STATUS_ACTIVE;
        $user->pass_last_change = Carbon::now();
        $user->pass_is_new = 1;
        $user->save();

        $this->command->info("User with email: $user->email saved");

        $role = Role::where('name', 'admin')->first();
        $user->assignRole($role);
        $this->command->info("Role $role->name was assigned to $user->names");
    }
}
