<?php

namespace App\Rules;

use App\Enums\PdoiSubjectDeliveryMethodsEnum;
use App\Http\Controllers\SsevController;
use Illuminate\Contracts\Validation\Rule;

class SsevEgovProfileRule implements Rule
{

    public $pdoiSubjectId;
    public $identityNumber;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($pdoiSubjectId, $identityNumber = '')
    {
        $this->pdoiSubjectId = $pdoiSubjectId;
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
        if( $value == PdoiSubjectDeliveryMethodsEnum::SDES->value ) {
            if( !SsevController::getEgovProfile($this->pdoiSubjectId, $this->identityNumber) ) {
                return false;
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
