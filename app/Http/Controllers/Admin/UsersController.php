<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;
use App\Models\CustomRole;
use App\Models\PdoiResponseSubject;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Mail\UsersChangePassword;

class  UsersController extends Controller
{
    use VerifiesEmails;

    /**
     * Display Users Table
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        if(!auth()->user()->canany('manage.*', 'users.*')) {
            return back()->with('danger', __('messages.no_rights_to_view_content'));
        }

        $name = ($request->filled('names')) ? $request->get('names') : null;
        $username = ($request->filled('username')) ? $request->get('username') : null;
        $email = ($request->filled('email')) ? $request->get('email') : null;
        $role_id = ($request->filled('role_id')) ? $request->get('role_id') : null;
        $active = $request->filled('active') ? $request->get('active') : 1;
        $paginate = $request->filled('paginate') ? $request->get('paginate') : User::PAGINATE;

        $roles = Role::whereActive(true)
            ->where('name', '<>', CustomRole::SUPER_USER_ROLE)
            ->orderBy('display_name', 'asc')
            ->get(['id','display_name']);

        //\DB::enableQueryLog();
        $users = User::with(['roles', 'responseSubject', 'responseSubject.translation'])
            ->when($role_id, function ($query, $role_id) {
                return $query->whereHas('roles', function ($q) use ($role_id) {
                    $q->where('id', $role_id);
                });
            })
            ->when($name, function ($query, $name) {
                return $query->where('names', 'ILIKE', "%$name%");
            })
            ->when($username, function ($query, $username) {
                return $query->where('username', 'ILIKE', "%$username%");
            })
            ->when($email, function ($query, $email) {
                return $query->where('email', 'ILIKE', "%$email%");
            })
            ->ByActiveState($active)
            ->orderBy('names', 'asc')
            ->paginate($paginate);
        //dd(\DB::getQueryLog());


        return $this->view('admin.users.index',
            compact('users', 'roles', 'username', 'email')
        );
    }

    /**
     * Export all user's list in excel
     *
     * @return Excel file
     */
    public function export()
    {
        $users = User::with('roles')->get();

        try {
            return Excel::download(new UsersExport($users), 'users.xlsx');
        }
        catch (Exception $e) {

            Log::error($e);

            return redirect()->back()->with('warning', "Възникна грешка при експортирането, моля опитайте отново");
        }
    }

    /**
     * Show create User form
     *
     * @return Response JSON formatted string
     */
    public function create()
    {
        if(!auth()->user()->canany('manage.*', 'users.*')) {
            return back()->with('danger', __('messages.no_rights_to_view_content'));
        }

        $roles = Role::whereActive(true)
            ->where('name', '<>', CustomRole::SUPER_USER_ROLE)
            ->orderBy('display_name', 'asc')->get();

        $perms = Permission::orderBy('id', 'asc')->get();
        $perms = groupPermissions($perms);
        $rzsSubjectOptions = PdoiResponseSubject::optionsList();

        return $this->view('admin.users.create', compact('roles', 'perms', 'rzsSubjectOptions'));
    }

    /**
     * Create new User record
     *
     * @param StoreUsersRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUsersRequest $request)
    {
        $must_change_password = ($request->filled('must_change_password')) ? true : null;
        $data = $request->validated();
//        $data = $request->except(['_token','password_confirmation','roles', 'permissions']);
        $roles = $data['roles'] ?? [];
        $permissions = $data['permissions'] ?? [];
        foreach (['_token','password_confirmation','roles', 'permissions'] as $key){
            unset($data[$key]);
        }

        DB::beginTransaction();

        try {
            $user = User::make($data);
            $user->is_public_contact = (int)(isset($data['is_public_contact']));
            if ($must_change_password) {
                $message = trans_choice('custom.users', 1)." {$data['username']} ".__('messages.created_successfully_m').". ".__('messages.email_send');
                Mail::to($data['email'])->send(new UsersChangePassword($user));
            } else {
                $message = trans_choice('custom.users', 1)." {$data['username']} ".__('messages.created_successfully_m');
                $user->password = bcrypt($data['password']);
                $user->pass_last_change = Carbon::now();
                $user->pass_is_new = 1;
            }

            $user->save();

            $user->syncRoles($roles);
            $user->syncPermissions($permissions);

            DB::commit();

            return to_route('admin.users')->with('success', $message);

        } catch (\Exception $e) {

            Log::error($e);

            DB::rollBack();

            return redirect()->back()->withInput($request->all())->with('danger', __('messages.system_error'));
        }

    }

    /**
     * Show edit User form
     *
     * @param  User  $user
     * @return View
     */
    public function edit(User $user)
    {
        if(!auth()->user()->canany('manage.*', 'users.*')) {
            return back()->with('danger', __('messages.no_rights_to_view_content'));
        }

        $roles = Role::whereActive(true)
            ->where('name', '<>', CustomRole::SUPER_USER_ROLE)
            ->orderBy('display_name', 'asc')->get();
        $perms = Permission::orderBy('id', 'asc')->get();
        $perms = groupPermissions($perms);
        $item = $user;
        $rzsSubjectOptions = PdoiResponseSubject::optionsList();

        return $this->view('admin.users.edit', compact('item', 'roles', 'perms', 'rzsSubjectOptions'));
    }

    /**
     * Update user data in database
     *
     * @param User $user
     * @param UpdateUsersRequest $request
     * @return RedirectResponse
     */
    public function update(User $user, UpdateUsersRequest $request)
    {
        $validated = $request->validated();
        $roles = $validated['roles'] ?? [];
        $permissions = $validated['permissions'] ?? [];
        foreach (['_token','password_confirmation','roles', 'permissions'] as $key){
            unset($validated[$key]);
        }

        DB::beginTransaction();

        try {
            if (isset($validated['password']) && !is_null($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
                $validated['pass_last_change'] = Carbon::now();
                $validated['pass_is_new'] = 1;
            }
            if(isset($validated['status']) && (in_array((int)$validated['status'], [User::STATUS_BLOCKED, User::STATUS_INACTIVE])) ) {
                $validated['active']  = 0;
            }
            if(isset($validated['status']) && (in_array((int)$validated['status'], [User::STATUS_ACTIVE, User::STATUS_REG_IN_PROCESS])) ) {
                $validated['active']  = 1;
            }
            $validated['is_public_contact'] = $validated['is_public_contact'] ?? 0;
            $user->fill($validated);
            $user->save();

            $user->syncRoles($roles);
            $user->syncPermissions($permissions);

            $user->save();
            DB::commit();

            return to_route('admin.users')
                ->with('success', trans_choice('custom.users', 1)." ".__('messages.updated_successfully_m'));

        } catch (\Exception $e) {

            Log::error($e);

            DB::rollBack();

            return to_route('admin.users')->with('danger', __('messages.system_error'));

        }
    }

    /**
     * Show edit own profile form
     *
     * @param User $user
     * @return View
     */
    public function editProfile(User $user)
    {
        return $this->view('admin.users.edit-profile', compact('user'));
    }

    /**
     * Update own profile data
     *
     * @param User $user
     * @param UpdateUsersRequest $request
     * @return RedirectResponse
     */
    public function updateProfile(User $user, ProfileUpdateRequest $request)
    {
        $data = $request->except(['_token']);

        try {

            $user->username = $data['username'];
            $user->names = $data['names'];

            if (!is_null($data['password'])) {
                $user->password = bcrypt($data['password']);
                $user->pass_last_change = Carbon::now();
                $user->pass_is_new = 1;
            }

            $user->save();

            auth()->setUser($user);

            return redirect()->back()->with('success', "Вашият профил беше обновен успешно");

        } catch (Exception $e) {

            Log::error($e);

            return redirect()->back()->with('danger', __('messages.system_error'));

        }
    }

    /**
     * Delete existing User record
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user)
    {
        try {

            foreach ($user->roles->pluck('id') as $role) {
                $user->removeRole($role);
            }
            $user->delete();

            return to_route('admin.users')
                ->with('success', trans_choice('custom.users', 1)." ".__('messages.deleted_successfully_m'));
        }
        catch (\Exception $e) {

            Log::error($e);
            return to_route('admin.users')->with('danger', __('messages.system_error'));

        }
    }

    public function myNotifications(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = $request->user();
        $notifications = $user->myNotifications()->paginate(User::PAGINATE);

        return $this->view('admin.my_notifications.index', compact('user', 'notifications'));
    }

    public function showMyNotifications(Request $request, $id): \Illuminate\View\View
    {
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();
        return $this->view('admin.my_notifications.show', compact('notification'));
    }
}
