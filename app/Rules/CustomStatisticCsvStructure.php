<?php

namespace App\Rules;

use App\Enums\CustomStatisticTypeEnum;
use Illuminate\Contracts\Validation\Rule;

class CustomStatisticCsvStructure implements Rule
{
    private $type;
    private $err_msg;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($type)
    {
        $this->type = $type;
        $this->err_msg = '';
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
        $check = true;
        switch ($this->type){
            case CustomStatisticTypeEnum::TYPE_BASE->value:
                
                break;
        }

        return $check;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->err_msg;
    }
}
