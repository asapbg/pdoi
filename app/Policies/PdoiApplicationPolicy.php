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
        return $user->can('manage.*') ||
            (
                $user->canany(['application.*', 'application.view'])
                && (
                    ($pdoiApplication->response_subject_id && $user->administrative_unit === $pdoiApplication->response_subject_id)
                    || (!$pdoiApplication->response_subject_id && $pdoiApplication->parent && $user->administrative_unit == $pdoiApplication->parent->response_subject_id)
                )
            );
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
     * Determine whether the user can update the model category.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateCategory(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return $user->can('manage.*') ||
            (
                $user->canany(['application.*', 'application.view', 'application.edit'])
                && (
                    ($pdoiApplication->response_subject_id && $user->administrative_unit === $pdoiApplication->response_subject_id)
                    || (!$pdoiApplication->response_subject_id && $pdoiApplication->parent && $user->administrative_unit == $pdoiApplication->parent->response_subject_id)
                )
            );
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
//        //TODO fix me add subject from events
//        return (PdoiApplicationStatusesEnum::canRenew($pdoiApplication->status) //is in forwarded status
//                || $pdoiApplication->status == PdoiApplicationStatusesEnum::RENEWED->value  //is in renew procedure
//                || in_array($pdoiApplication->status, PdoiApplicationStatusesEnum::notCompleted()))
//            && (
//                $user->can('manage.*') ||
//                (
//                    $user->canany(['application.*', 'application.view', 'application.edit'])
//                    && (
//                        ($pdoiApplication->response_subject_id && $user->administrative_unit === $pdoiApplication->response_subject_id)
//                        || (!$pdoiApplication->response_subject_id && $pdoiApplication->parent && $user->administrative_unit == $pdoiApplication->parent->response_subject_id)
//                    )
//                )
//            );

        return ($pdoiApplication->status == PdoiApplicationStatusesEnum::RENEWED->value  //is in renew procedure
                || in_array($pdoiApplication->status, PdoiApplicationStatusesEnum::notCompleted())
                || PdoiApplicationStatusesEnum::canEditFinalDecision($pdoiApplication->status)
            )
            && (
                $user->can('manage.*') ||
                (
                    $user->canany(['application.*', 'application.view', 'application.edit'])
                    && (
                        ($pdoiApplication->response_subject_id && $user->administrative_unit === $pdoiApplication->response_subject_id)
                        || (!$pdoiApplication->response_subject_id && $pdoiApplication->parent && $user->administrative_unit == $pdoiApplication->parent->response_subject_id)
                    )
                )
            );
    }

    /**
     * Determine whether the user can set final decision on expired application.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateExpired(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return ($pdoiApplication->status == PdoiApplicationStatusesEnum::NO_REVIEW->value  //expired
                && !$pdoiApplication->events()->whereIn('event_type', ApplicationEventsEnum::notAllowDecisionToExpiredApplication())->count()
            )
            && (
                $user->can('manage.*') ||
                (
                    $user->canany(['application.*', 'application.view', 'application.edit'])
                    && (
                        ($pdoiApplication->response_subject_id && $user->administrative_unit === $pdoiApplication->response_subject_id)
                        || (!$pdoiApplication->response_subject_id && $pdoiApplication->parent && $user->administrative_unit == $pdoiApplication->parent->response_subject_id)
                    )
                )
            );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function canEditFinalDecision(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return (PdoiApplicationStatusesEnum::canEditFinalDecision($pdoiApplication->status))
            && (
                $user->can('manage.*') ||
                (
                    $user->canany(['application.*', 'application.view', 'application.edit'])
                    && (
                        ($pdoiApplication->response_subject_id && $user->administrative_unit === $pdoiApplication->response_subject_id)
                        || (!$pdoiApplication->response_subject_id && $pdoiApplication->parent && $user->administrative_unit == $pdoiApplication->parent->response_subject_id)
                    )
                )
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
                $user->can('manage.*') ||
                (
                    $user->canany(['application.*', 'application.view', 'application.edit'])
                    && (
                        ($pdoiApplication->response_subject_id && $user->administrative_unit === $pdoiApplication->response_subject_id)
                        || (!$pdoiApplication->response_subject_id && $pdoiApplication->parent && $user->administrative_unit == $pdoiApplication->parent->response_subject_id)
                    )
                )
            );
    }

    /**
     * Determine whether the user can forward the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forward(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return PdoiApplicationStatusesEnum::canForward($pdoiApplication->status)// status allow forwarding
            && !$pdoiApplication->manual && $pdoiApplication->response_subject_id
            && (
                $user->can('manage.*') ||
                (
                    $user->canany(['application.*', 'application.view', 'application.edit'])
                    && (
                        ($pdoiApplication->response_subject_id && $user->administrative_unit === $pdoiApplication->response_subject_id)
                        || (!$pdoiApplication->response_subject_id && $pdoiApplication->parent && $user->administrative_unit == $pdoiApplication->parent->response_subject_id)
                    )
                )
            );
    }

    /**
     * Determine whether the user can send Extra Info the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PdoiApplication  $pdoiApplication
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function addExtraInfo(User $user, PdoiApplication $pdoiApplication): \Illuminate\Auth\Access\Response|bool
    {
        return $pdoiApplication->lastEvent
            && $pdoiApplication->lastEvent->event_type == ApplicationEventsEnum::ASK_FOR_INFO->value
            && $user->id == $pdoiApplication->user_reg;
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
