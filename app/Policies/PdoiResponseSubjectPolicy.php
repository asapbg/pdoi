<?php

namespace App\Policies;

use App\Models\CustomRole;
use App\Models\PdoiResponseSubject;
use App\Models\RzsSection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PdoiResponseSubjectPolicy
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
        return $user->canAny(['manage.*','administration.*', 'administration.rzs_items']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiResponseSubject  $pdoiResponseSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PdoiResponseSubject $pdoiResponseSubject)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.rzs_items']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.rzs_items']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiResponseSubject  $pdoiResponseSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PdoiResponseSubject $pdoiResponseSubject)
    {
        $user = auth()->user();
        if($user && !$user->hasAnyRole([CustomRole::ADMIN_USER_ROLE, CustomRole::SUPER_USER_ROLE])){
            $ids = RzsSection::getAdmStructureIds($user->responseSubject->adm_level);
        }

        return $user->canAny(['manage.*','administration.*', 'administration.rzs_items'])
            && !$pdoiResponseSubject->adm_register
            && (
                !isset($ids)
                || (
                    $pdoiResponseSubject->section && in_array($pdoiResponseSubject->section->id, $ids)
                    && ($pdoiResponseSubject->section->id != $user->responseSubject->section->id || $user->responseSubject->id == $pdoiResponseSubject->id)
                )
            );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiResponseSubject  $pdoiResponseSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateSettings(User $user, PdoiResponseSubject $pdoiResponseSubject)
    {
        $user = auth()->user();
        if($user && !$user->hasAnyRole([CustomRole::ADMIN_USER_ROLE, CustomRole::SUPER_USER_ROLE])){
            $ids = RzsSection::getAdmStructureIds($user->responseSubject->adm_level);
        }

        return $user->canAny(['manage.*','administration.*', 'administration.rzs_items'])
            && (
                !isset($ids)
                || (
                    $pdoiResponseSubject->section && in_array($pdoiResponseSubject->section->id, $ids)
                    && ($pdoiResponseSubject->section->id != $user->responseSubject->section->id || $user->responseSubject->id == $pdoiResponseSubject->id)
                )
            );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiResponseSubject  $pdoiResponseSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PdoiResponseSubject $pdoiResponseSubject)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.rzs_items'])
            && !$pdoiResponseSubject->adm_register && !$pdoiResponseSubject->trashed();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiResponseSubject  $pdoiResponseSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, PdoiResponseSubject $pdoiResponseSubject)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.rzs_items'])
            && !$pdoiResponseSubject->adm_register && $pdoiResponseSubject->trashed();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiResponseSubject  $pdoiResponseSubject
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, PdoiResponseSubject $pdoiResponseSubject)
    {
        return false;
    }
}
