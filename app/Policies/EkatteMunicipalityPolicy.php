<?php

namespace App\Policies;

use App\Models\EkatteMunicipality;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EkatteMunicipalityPolicy
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
     * @param  \App\Models\EkatteMunicipality  $ekatteMunicipality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EkatteMunicipality $ekatteMunicipality)
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
     * @param  \App\Models\EkatteMunicipality  $ekatteMunicipality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EkatteMunicipality $ekatteMunicipality)
    {
        return $user->canAny(['manage.*','administration.*', 'administration.system_classification']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EkatteMunicipality  $ekatteMunicipality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EkatteMunicipality $ekatteMunicipality)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EkatteMunicipality  $ekatteMunicipality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, EkatteMunicipality $ekatteMunicipality)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\EkatteMunicipality  $ekatteMunicipality
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, EkatteMunicipality $ekatteMunicipality)
    {
        return false;
    }
}
