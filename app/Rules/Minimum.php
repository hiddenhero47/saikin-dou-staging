<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * The field under validation must have a minimum value. 
 * Strings, numerics, arrays, and files are evaluated in the same fashion as the size rule.
 * 
 * $request->validate([ 'field' => [new Minimum(3, 'field1', 'field2')] ]);
 */
class Minimum implements Rule
{
    /**
     * Minimum number required.
     *
     * @var int
     */
    protected int $min;

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
    public function __construct(int $min, ...$fields)
    {
        $this->min = $min;
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

        return $total >= $this->min;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The combined :attribute, '.implode(', ',$this->fields).' fields are below the required minimum value of '.$this->min;
    }
}
