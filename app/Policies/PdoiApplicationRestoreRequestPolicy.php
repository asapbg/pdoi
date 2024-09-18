<?php

namespace App\Policies;

use App\Enums\ApplicationEventsEnum;
use App\Enums\PdoiApplicationStatusesEnum;
use App\Models\PdoiApplicationRestoreRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PdoiApplicationRestoreRequestPolicy
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
     * @param  \App\Models\PdoiApplicationRestoreRequest  $pdoiApplicationRestoreRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PdoiApplicationRestoreRequest $pdoiApplicationRestoreRequest)
    {
        return $user->can('manage.*') ||
            (
                $user->canany(['application.*', 'application.view'])
                && (
                    ($pdoiApplicationRestoreRequest->application->response_subject_id && $user->administrative_unit === $pdoiApplicationRestoreRequest->application->response_subject_id)
                    || (!$pdoiApplicationRestoreRequest->application->response_subject_id && $pdoiApplicationRestoreRequest->application->parent && $user->administrative_unit == $pdoiApplicationRestoreRequest->application->parent->response_subject_id)
                )
            );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplicationRestoreRequest  $pdoiApplicationRestoreRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PdoiApplicationRestoreRequest $pdoiApplicationRestoreRequest)
    {
        return PdoiApplicationStatusesEnum::canRenew($pdoiApplicationRestoreRequest->application->status)// status allow renewing
            && !$pdoiApplicationRestoreRequest->application->manual
            && $pdoiApplicationRestoreRequest->application->currentEvent->event->app_event != ApplicationEventsEnum::RENEW_PROCEDURE->value //check if already has renewed event with rejection
            && (
                $user->can('manage.*') ||
                (
                    $user->canany(['application.*', 'application.view', 'application.edit'])
                    && (
                        ($pdoiApplicationRestoreRequest->application->response_subject_id && $user->administrative_unit === $pdoiApplicationRestoreRequest->application->response_subject_id)
                        || (!$pdoiApplicationRestoreRequest->application->response_subject_id && $pdoiApplicationRestoreRequest->application->parent && $user->administrative_unit == $pdoiApplicationRestoreRequest->application->parent->response_subject_id)
                    )
                )
            )
            && $pdoiApplicationRestoreRequest->status == PdoiApplicationRestoreRequest::STATUS_IN_PROCESS;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplicationRestoreRequest  $pdoiApplicationRestoreRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PdoiApplicationRestoreRequest $pdoiApplicationRestoreRequest)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplicationRestoreRequest  $pdoiApplicationRestoreRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, PdoiApplicationRestoreRequest $pdoiApplicationRestoreRequest)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplicationRestoreRequest  $pdoiApplicationRestoreRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, PdoiApplicationRestoreRequest $pdoiApplicationRestoreRequest)
    {
        return false;
    }
}
