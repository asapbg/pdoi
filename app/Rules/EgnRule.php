<?php

namespace App\Rules;

use App\Models\FormModel;
use Egn;
use Illuminate\Contracts\Validation\Rule;

class EgnRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Egn::valid($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Невалидно ЕГН';
    }
}
