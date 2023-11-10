<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('users')->truncate();
        //=== Users ====
        $oldUsers = DB::connection('old')->select('
            select
                adm_users.user_id as id,
                adm_groups.group_name,
                adm_users.username,
                adm_users.password,
                adm_users.user_type,
                adm_users.names,
                adm_users.lang,
                adm_users.email,
                adm_users.phone,
                adm_users.status,
                adm_users.date_reg as created_at,
                adm_users.user_reg,
                adm_users.date_last_mod as updated_at,
                coalesce(adm_users.login_attempts, 0) as login_attempts,
                adm_users.pass_last_change,
                adm_users.pass_is_new,
                case when status <> '.User::STATUS_INACTIVE.' then 1 else 0 end as active,
                case when status <> '.User::STATUS_REG_IN_PROCESS.' then null else adm_users.date_reg end as email_verified_at,
                adm_users.status_date,
                adm_users.status_explain,
                adm_users.org_id,
                adm_users.org_code,
                adm_users.org_code as administrative_unit,
                adm_users.org_text,
                adm_users.position,
                adm_users.position_text,
                adm_users.mail_forward,
                adm_users.mail_user,
                adm_users.mail_pass,
                adm_users.mail_email,
                adm_users.mail_imap_server,
                adm_users.mail_smtp_server,
                adm_users.user_last_mod,
                adm_users.reg_token,
                adm_users.reg_token_expire,
                adm_users.pass_rec_token,
                adm_users.pass_rec_expire
            from adm_users
            -- roles
            left join adm_user_group on adm_user_group.user_id = adm_users.user_id
            left join adm_groups on adm_groups.group_id = adm_user_group.group_id
            -- subject
            -- left join pdoi_response_subject on pdoi_response_subject.id = adm_users.org_code
            group by adm_users.user_id, adm_groups.group_id
            order by adm_users.user_id asc
            ');

        if( sizeof($oldUsers) ) {
            $oldUsersChunks = array_chunk($oldUsers, 50);
            if( sizeof($oldUsersChunks) ) {
                //get roles
                $adminRole = Role::findByName('admin');
                $adminModeratorRole = Role::findByName('admin_moderator');
                $webRole = Role::findByName('external_user');

                DB::beginTransaction();
                try {
                    foreach ($oldUsersChunks as $users) {
                        $adminIds = $adminModeratorIds = $webUsersIds = $newUsers = array();
                        foreach ($users as $user) {
                            if( $user->group_name == 'Администратор на платформата' ) {
                                $adminIds[] = $user->id;
                            } elseif ( $user->group_name == 'Администратор-модератор' ) {
                                $adminModeratorIds[] = $user->id;
                            } else {
                                $webUsersIds[] = $user->id;
                            }
                            $userToArray = get_object_vars($user);
                            $p = base64_decode($userToArray['password']);
                            $userToArray['password'] = bcrypt($p);
                            unset($userToArray['group_name']);
                            $newUsers[] = $userToArray;
                        }
                        if( sizeof($newUsers) ){
                            User::insert($newUsers);
                        }
                        //assign internal admin role
                        if( sizeof($adminIds) ){
                            $adminRole->users()->attach($adminIds);
                        }
                        //assign internal admin moderator role
                        if( sizeof($adminModeratorIds) ){
                            $adminModeratorRole->users()->attach($adminModeratorIds);
                        }
                        //assign external user role
                        if( sizeof($webUsersIds) ){
                            $webRole->users()->attach($webUsersIds);
                        }
                        //TODO get moderator pdoi_response_subjects if this is not the field adm_users.org_code
                        //TODO assign individual permissions
                    }

                    DB::commit();
                } catch (\Exception $e){
                    Log::error('Migration old users: '. $e->getMessage());
                    DB::rollBack();
                }
            }
        }

        \Illuminate\Support\Facades\DB::statement(
            "do $$
                    declare newId int;
                    begin
                        select (coalesce(max(id),0) +1)  from users into newId;
                        execute 'alter SEQUENCE users_id_seq RESTART with '|| newId;
                    end;
                    $$ language plpgsql"
        );

        $localUsers = array(
            [
                'role' => 'service_user',
                'names' => 'Сервизен потребител',
                'email' => 'service_user@asap.bg',
            ],
            [
                'role' => 'admin',
                'names' => 'Администратор',
                'email' => 'admin@asap.bg',
            ],
            [
                'role' => 'admin',
                'names' => 'Админ',
                'email' => 'admin@gov.bg',
            ]
        );

        foreach ($localUsers as $u) {
            $user = new User;
            $user->username = $u['email'];
            $user->password = bcrypt('pass123');
            $user->user_type = User::USER_TYPE_INTERNAL;
            $user->names = $u['names'];
            $user->email = $u['email'];
            $user->status = User::STATUS_ACTIVE;
            $user->pass_last_change = Carbon::now();
            $user->pass_is_new = 1;
            $user->save();
            $this->command->info("User with email: $user->email saved");

            $role = Role::where('name', $u['role'])->first();
            $user->assignRole($role);
            $this->command->info("Role $role->name was assigned to $user->names");
        }
    }
}
