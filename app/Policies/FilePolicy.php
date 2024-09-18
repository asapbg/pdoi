<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\File  $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function download(User $user, File $file): \Illuminate\Auth\Access\Response|bool
    {
        $application = $file->code_object == File::CODE_OBJ_APPLICATION ? $file->application : ($file->code_object == File::CODE_OBJ_APPLICATION_RENEW ? $file->renew->application : $file->event->application);
        if( !$file->visible_on_site
            && !(
                $application->applicant->id == $user->id
                || ($user->can('manage.*') || $user->canany(['application.*', 'application.view', 'application.edit']))
            )
        ) {
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\File  $file
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, File $file)
    {
        if( ($file->code_object == File::CODE_OBJ_MENU_SECTION
            || $file->code_object == File::CODE_OBJ_PAGE)
        ) {
            return true;
        }
        return false;
    }
}
