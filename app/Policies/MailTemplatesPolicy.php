<?php

namespace App\Policies;

use App\Enums\MailTemplateTypesEnum;
use App\Models\MailTemplates;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MailTemplatesPolicy
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
        return $user->canany(['manage.*', 'application.*', 'application.mail_templates']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, MailTemplates $mailTemplates)
    {
        return $user->canany(['manage.*', 'application.*', 'application.mail_templates']);
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
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, MailTemplates $mailTemplates)
    {
        return $user->canany(['manage.*', 'application.*', 'application.mail_templates']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, MailTemplates $mailTemplates)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, MailTemplates $mailTemplates)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MailTemplates  $mailTemplates
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, MailTemplates $mailTemplates)
    {
        return false;
    }
}
