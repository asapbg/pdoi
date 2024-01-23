<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FileClientMimeType implements Rule
{
    protected $mimeTypes;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($allowedTypes)
    {
        $this->mimeTypes = $allowedTypes;
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
        if(!in_array($value->getClientMimeType(), $this->mimeTypes)){
            return false;
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
        return __('validation.mimes', ['attribute' => 'sdfsdf', 'values' => implode('|', \App\Models\File::ALLOWED_FILE_EXTENSIONS)]);
    }
}
