<?php

namespace App\Models;


use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;
    use CausesActivity;
    use Notifiable;
    use HasRoles;
    use MustVerifyEmail;

    protected string $logName = "users";

    const MODULE_NAME = ('custom.module_users');

    const PAGINATE = 20;

    const USER_TYPE_EXTERNAL = 2;
    const USER_TYPE_INTERNAL = 1;

    const EXTERNAL_USER_DEFAULT_ROLE = 'external_user';

    //Legal form
    const USER_TYPE_PERSON = 1;
    const USER_TYPE_COMPANY = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's name
     */
    public function getModelName() {
        return $this->fullName();
    }

    /**
     * Change activity log description on login
     *
     * @param Activity $activity
     */
    public function tapActivity(Activity $activity)
    {
        if (request()->path() == "admin/login") {
            $activity->description = "user_login";
        }
    }

    const STATUS_ACTIVE = 2;
    const STATUS_INACTIVE = 4;
    const STATUS_BLOCKED = 3;
    const STATUS_REG_IN_PROCESS = 1;

    /**
     * Get user statuses
     *
     * @return array
     */
    public static function getUserStatuses(): array
    {
        return [
            self::STATUS_ACTIVE     => __('custom.active_m'),
            self::STATUS_INACTIVE   => __('custom.inactive_m'),
            self::STATUS_BLOCKED    => __('custom.blocked'),
            self::STATUS_REG_IN_PROCESS    => __('custom.in_reg_process')
        ];
    }

    /**
     * Get user types
     *
     * @return array
     */
    public static function getUserTypes(): array
    {
        return [
            self::USER_TYPE_INTERNAL     => __('custom.users.type.'.self::USER_TYPE_INTERNAL),
            self::USER_TYPE_EXTERNAL   => __('custom.users.type.'.self::USER_TYPE_EXTERNAL),
        ];
    }

    /**
     * Get user types
     *
     * @return array
     */
    public static function getUserLegalForms(): array
    {
        return [
            self::USER_TYPE_PERSON     => __('custom.users.legal_form.'.self::USER_TYPE_INTERNAL),
            self::USER_TYPE_COMPANY   => __('custom.users.legal_form.'.self::USER_TYPE_EXTERNAL),
        ];
    }

    /**
     * Log user activity
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->logName);
    }

    /**
     * Get the user's activities
     *
     * @return hasMany
     */
    public function activities()
    {
        return $this->hasMany(CustomActivity::class, 'causer_id', 'id');
    }

    /**
     * Return the user's full name if not empty
     * else return the username
     */
    public function fullName()
    {
        return $this->names;
    }

    protected function active(): Attribute
    {
        return Attribute::make(
            get: fn () => !($this->status == self::STATUS_INACTIVE),
        );
    }

    protected function legalForm(): Attribute
    {
        return Attribute::make(
            get: fn () => (!empty($this->person_identity) ? self::USER_TYPE_PERSON : (!empty($this->company_identity) ? self::USER_TYPE_COMPANY : 0)),
        );
    }

    public function scopeIsActive($query)
    {
        $query->where('users.status', '<>', self::STATUS_INACTIVE)
            ->where('users.active', 1);
    }

    public function scopeIsInactive($query)
    {
        $query->where('users.status', self::STATUS_INACTIVE)
            ->where('users.active', 0);
    }

    public function scopeNotVerified($query, $id)
    {
        $query->where('users.status', self::STATUS_REG_IN_PROCESS)
            ->whereNull('users.email_verified_at')
            ->where('users.id', '=', $id)
            ->where('users.user_type', '=', User::USER_TYPE_EXTERNAL);
    }

    public function scopeIsInProcess($query)
    {
        $query->where('users.status', self::STATUS_REG_IN_PROCESS);
    }

    public function scopeByActiveState($query, $active)
    {
        if( $active ) {
            $query->IsActive();
        } else{
            $query->IsInactive();
        }
    }

}
