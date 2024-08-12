<?php

namespace App\Policies;

use App\Models\NoConsiderReason;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NoConsiderReasonPolicy
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
        return $user->canAny(['manage.*','administration.*', 'administration.system_classification']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NoConsiderReason  $noConsiderationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, NoConsiderReason $noConsiderationReason)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.system_classification']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.system_classification']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NoConsiderReason  $noConsiderationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, NoConsiderReason $noConsiderationReason)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.system_classification']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NoConsiderReason  $noConsiderationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, NoConsiderReason $noConsiderationReason)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NoConsiderReason  $noConsiderationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, NoConsiderReason $noConsiderationReason)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NoConsiderReason  $noConsiderationReason
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, NoConsiderReason $noConsiderationReason)
    {
        //
    }
}
