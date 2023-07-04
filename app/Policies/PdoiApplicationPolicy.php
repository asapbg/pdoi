<?php

namespace App\Policies;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\PdoiApplication;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PdoiApplicationPolicy
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
        return $user->canany(['manage.*', 'application.*', 'application.view']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        //TODO fix me add subject from events
        return $user->can('manage.*') || ($user->canany(['application.*', 'application.view'])
            && $user->administrative_unit === $pdoiApplication->responseSubject->id);
    }

    /**
     * Determine whether the user can view the model in front as own application.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewMy(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return $user->id == $pdoiApplication->applicant->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return $user->canany(['manage.*', 'pdoi.*', 'pdoi.web']);
    }

    /**
     * Determine whether the user can create manual models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createManual(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return $user->can('manage.*') || $user->canany(['application.*', 'application.edit']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {

        //TODO fix me add subject from events
        return ($pdoiApplication->status == PdoiApplicationStatusesEnum::RENEWED->value  //is in renew procedure
                || in_array($pdoiApplication->status, PdoiApplicationStatusesEnum::notCompleted()))
            && (
                $user->can('manage.*') || ($user->canany(['application.*', 'application.view', 'application.edit'])
                && $user->administrative_unit === $pdoiApplication->responseSubject->id)
            );
    }

    /**
     * Determine whether the user can renew the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function renew(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return PdoiApplicationStatusesEnum::canRenew($pdoiApplication->status)// status allow renewing
            && !$pdoiApplication->manual
            && $pdoiApplication->currentEvent->event->app_event != ApplicationEventsEnum::RENEW_PROCEDURE->value //check if already has renewed event with rejection
            && (
                $user->can('manage.*') || ($user->canany(['application.*', 'application.view', 'application.edit'])
                    && $user->administrative_unit === $pdoiApplication->responseSubject->id)
            );
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return false;
    }
}
