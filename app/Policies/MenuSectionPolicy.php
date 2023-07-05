<?php

namespace App\Policies;

use App\Models\MenuSection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuSectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->canAny(['manage.*','menu_section.*', 'menu_section.section']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MenuSection  $menuSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, MenuSection $menuSection)
    {
        return $user->canAny(['manage.*','menu_section.*', 'menu_section.section']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->canAny(['manage.*','menu_section.*', 'menu_section.section']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MenuSection  $menuSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, MenuSection $menuSection)
    {
        return $user->canAny(['manage.*','menu_section.*', 'menu_section.section']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MenuSection  $menuSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, MenuSection $menuSection)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MenuSection  $menuSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, MenuSection $menuSection)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MenuSection  $menuSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, MenuSection $menuSection)
    {
        return false;
    }
}
