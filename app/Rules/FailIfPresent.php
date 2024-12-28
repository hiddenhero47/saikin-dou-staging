<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * The field under validation can not be present if another_field is present.
 * 
 * $request->validate([ 'field' => [new FailIfPresent('another_field')] ])
 */
class FailIfPresent implements Rule
{
    /**
     *  Holds the name of another field
     *
     * @var string
    */
    protected string $another_field = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($field)
    {
        $this->another_field = $field;
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
        return !request()->exists($this->another_field);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute field and '.$this->another_field.' field can not both be present.';
    }
}
