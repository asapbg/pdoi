<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class RolesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $active = $request->filled('active') ? $request->get('active') : 1;
        $roles = CustomRole::orderBy('display_name', 'asc')
            ->whereActive($active)
            ->where('name', '<>', CustomRole::SUPER_USER_ROLE)
            ->withCount('users')
            ->paginate(20);

        return $this->view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $guards = array_keys(config('auth.guards'));

        return $this->view('admin.roles.create', compact('guards'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'alias' => 'required|string|min:3|max:255',
            'display_name' => 'required|string|min:3|max:255'
        ]);

        $alias = $request->offsetGet('alias');
        $display_name = $request->offsetGet('display_name');

        try {
            CustomRole::create([
                'name' => $alias,
                'display_name' => $display_name,
                'guard_name' => config('auth.defaults.guard'),
            ]);

            return to_route('admin.roles')
                ->with('success', trans_choice('custom.roles', 1)." $display_name ".__('messages.created_successfully_f'));
        }
        catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()->withInput($request->all())->with('danger', __('messages.system_error'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  CustomRole $role
     * @return \Illuminate\Http\Response
     */
    public function show(CustomRole $role)
    {
        $this->authorize('view', CustomRole::class);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  CustomRole $role
     * @return View
     */
    public function edit(CustomRole $role)
    {
        //dd($role->users->count());
        $guards = array_keys(config('auth.guards'));
        $usersToAdd = User::whereDoesntHave('roles', function ($query) use($role) {
                        return $query->where('name',$role->name);
                        })
                        ->orderBy('names')->get();
        $roleUsers = $role->users()->get();
        return $this->view('admin.roles.edit', compact('role','guards', 'usersToAdd', 'roleUsers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  CustomRole $role
     * @return RedirectResponse
     */
    public function update(Request $request, CustomRole $role)
    {
        $request->validate([
            'alias' => 'required|string|min:3|max:255',
            'display_name' => 'required|string|min:3|max:255'
        ]);

        $alias = $request->offsetGet('alias');
        $display_name = $request->offsetGet('display_name');

        try {
            $role->update([
                'name' => $alias,
                'display_name' => $display_name,
            ]);

            return to_route('admin.roles')
                ->with('success', trans_choice('custom.roles', 1)." $display_name ".__('messages.created_successfully_f'));
        }
        catch (\Exception $e) {
            \Log::error($e);
            return redirect()->back()->withInput($request->all())->with('danger', __('messages.system_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CustomRole $role
     * @return RedirectResponse
     */
    public function destroy(CustomRole $role)
    {
        try {
            $role->active = false;
            $role->save();
            $role->delete();

            return to_route('admin.roles')
                ->with('success', trans_choice('custom.users', 1)." ".__('messages.deleted_successfully_m'));
        }
        catch (\Exception $e) {

            \Log::error($e);
            return redirect()->back()->withInput(request()->all())->with('danger', __('messages.system_error'));

        }
    }

    public function addUsers(CustomRole $role, Request $request)
    {
        if( !$request->filled('add_users') || !is_array($request->get('add_users')) || !sizeof($request->get('add_users')) ) {
            return back()->with('danger', __('custom.roles.validation.users'));
        }
        $users = $request->get('add_users');
        $role->users()->attach($users);

        return to_route('admin.roles.edit', ['role' => $role->id])
            ->with('success', __('custom.roles.success_update'));
    }

    public function removeUsers(CustomRole $role, Request $request)
    {
        if( !$request->filled('remove_users') || !is_array($request->get('remove_users')) || !sizeof($request->get('remove_users')) ) {
            return back()->with('danger', __('custom.roles.validation.users'));
        }

        $users = $request->get('remove_users');
        $role->users()->detach($users);

        return to_route('admin.roles.edit', ['role' => $role->id])
            ->with('success', __('custom.roles.success_update'));
    }
}
