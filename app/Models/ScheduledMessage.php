<?php

namespace App\Models;

use App\Traits\FilterSort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledMessage extends ModelActivityExtend
{
    use FilterSort, SoftDeletes;

    const PAGINATE = 20;
    const MODULE_NAME = 'custom.scheduled_message';
    protected $table = 'scheduled_message';
    //activity
    protected string $logName = "scheduled_message";

    public function sender(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function subject(): Attribute
    {
        $data = json_decode($this->data, true);
        return Attribute::make(
            get: fn () => $data && isset($data['subject']) ? $data['subject'] : ''
        );
    }

    public function recipients()
    {
        $userIds = json_decode($this->send_to);
        if(!sizeof($userIds)){
            return null;
        }

        return User::whereIn('id', $userIds)->withTrashed()->get();
    }

    public function notReceivedByMail()
    {
        $userIds = json_decode($this->not_send_to_by_email);
        if(!sizeof($userIds)){
            return null;
        }

        return User::whereIn('id', $userIds)->withTrashed()->get();
    }

    public function notReceivedByApp()
    {
        $userIds = json_decode($this->not_send_to_by_app);
        if(!sizeof($userIds)){
            return null;
        }

        return User::whereIn('id', $userIds)->withTrashed()->get();
    }
}
