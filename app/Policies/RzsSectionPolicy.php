<?php

namespace App\Policies;

use App\Models\CustomRole;
use App\Models\RzsSection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RzsSectionPolicy
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
        return $user->canAny(['manage.*','administration.*', 'administration.rzs_sections']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RzsSection  $rzsSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, RzsSection $rzsSection)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.rzs_sections']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.rzs_sections']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RzsSection  $rzsSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, RzsSection $rzsSection)
    {
        $user = auth()->user();
//        if($user && !$user->hasAnyRole([CustomRole::ADMIN_USER_ROLE, CustomRole::SUPER_USER_ROLE])){
//            $ids = RzsSection::getAdmStructureIds($user->responseSubject->adm_level);
//        }

        return $user->canAny(['manage.*','administration.*', 'administration.rzs_sections'])
            && $rzsSection->manual && (!isset($ids) || (isset($ids) && in_array($rzsSection->id, $ids)));
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RzsSection  $rzsSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, RzsSection $rzsSection)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RzsSection  $rzsSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, RzsSection $rzsSection)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\RzsSection  $rzsSection
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, RzsSection $rzsSection)
    {
        return false;
    }
}
