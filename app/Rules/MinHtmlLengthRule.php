<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MinHtmlLengthRule implements Rule
{
    public $length;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($length = 1)
    {
        $this->length = $length;
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
        return strlen(trim(stripHtmlTags($value, ['all']))) >= $this->length;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Полето трябва да съдържа минимум '.$this->length.' '.($this->length > 1 ? 'символа' : 'символ');
    }
}
