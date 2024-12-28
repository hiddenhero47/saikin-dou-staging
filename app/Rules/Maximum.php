<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * The field under validation must be less than or equal to a maximum value. 
 * Strings, numerics, arrays, and files are evaluated in the same fashion as the size rule.
 * 
 * $request->validate([ 'field' => [new Maximum(3, 'field1', 'field2')] ]);
 */
class Maximum implements Rule
{
    /**
     * Maximum number allowed.
     *
     * @var int
     */
    protected int $max;

    /**
     * Fields to be validated.
     *
     * @var array
     */
    protected array $fields;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $max, ...$fields)
    {
        $this->max = $max;
        $this->fields = $fields;
    }

    /**
     * Adapt a value to a countable form
     * 
     * @param string $key
     * @param mixed $var
     * @return array
     */
    public function adaptAndCount(string $key, $var)
    {
        if (is_array($var)) {
            return count($var);
        }

        if (is_numeric($var) || is_null($var)) {
            return $var;
        }

        if (is_string($var)) {
            return strlen($var);
        }

        return null;
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
        $total = $this->adaptAndCount($attribute, $value);

        foreach ($this->fields as $field) {
            $total += $this->adaptAndCount($field, request()->input($field));
        }

        return $total <= $this->max;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The combined :attribute, '.implode(', ',$this->fields).' fields exceeds the maximum allowed value of '.$this->max;
    }
}
