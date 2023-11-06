<?php

namespace App\Rules;

use App\Enums\DeliveryMethodsEnum;
use App\Http\Controllers\SsevController;
use Illuminate\Contracts\Validation\Rule;

class SsevProfileRule implements Rule
{
    public $user;
    public $identityType;
    public $identityNumber;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user, $identityType = '', $identityNumber = '')
    {
        $this->user = $user;
        $this->identityType = $identityType;
        $this->identityNumber = $identityNumber;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if( $value == DeliveryMethodsEnum::SDES->value ) {
            if( !$this->user->ssev_profile_id ) {
                if( !SsevController::getSsevProfile($this->user, $this->identityType, $this->identityNumber) ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.custom.ssev_profile_not_exist');
    }
}
