<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

/**
 * Check the difference between two fields with valid date time
 * and ensure it is not greater than a given range.
 * 
 * Please note that strtotime was used for date string validation
 * which means that strings of 'now','next Thursday','last Monday'
 * etc will be validate as valid date time strings.
 * 
 * If you wish to avoid this, additional validation is recommend
 * for the fields under validation.
 * 
 * $request->validate([ 
 *     'start_date' => [new DateDifference(['compared_with' => 'end_date', 'max_difference'=>'5 months'])],
 *     'end_date' => [new DateDifference(['compared_with' => 'start_date', 'max_difference' => '5 months'])],
 * ]);
 */
class DateDifference implements Rule
{
    /**
     * Holds the constraints
     *
     * @var array
    */
    protected array $constraints = [];

    /**
     *  Holds the date difference failed validations
     *
     * @var array
    */
    protected array $message_bag = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * Get all values in the message bag
     * 
     * @param string|void $var
     * @param array
     */
    public function getFromMessageBag($var = null)
    {
        return $var? $this->message_bag[$var] : $this->message_bag;
    }

    /**
     * Append a value to the message bag
     * 
     * @param array $var
     * @return array
     */
    public function appendToMessageBag(array $var)
    {
        return $this->message_bag = array_merge($this->message_bag,$var);
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
        // Check if string is a valid date
        if (!$value || !is_string($value) || !strtotime($value)) {
            $this->appendToMessageBag([':attribute field is not a valid date string.']);
        }

        if (!isset($this->constraints['compared_with'])) {
            $this->appendToMessageBag([':attribute field has no comparison field.']);
        }

        if (isset($this->constraints['compared_with']) && (!is_string(request()->input($this->constraints['compared_with'])) || !strtotime(request()->input($this->constraints['compared_with'])))) {
            $this->appendToMessageBag([$this->constraints['compared_with'].' field is not a valid date string.']);
        }

        if (!isset($this->constraints['max_difference'])) {
            $this->appendToMessageBag([':attribute requires a maximum difference for comparison.']);
        }

        if (isset($this->constraints['max_difference']) && !strtotime($this->constraints['max_difference'])) {
            $this->appendToMessageBag([':attribute stated maximum difference is not a valid date string.']);
        }

        // Check if they are errors
        if (!empty($this->getFromMessageBag())) {
            return false;
        }

        // Convert date time strings in to carbon date time strings
        $source_date = Carbon::parse($value);
        $target_date = Carbon::parse(request()->input($this->constraints['compared_with']));
        $minutes_between = $source_date->diffInMinutes($target_date);
        $minutes_allowed = Carbon::parse($this->constraints['max_difference'])->diffInMinutes();

        // Check if the total days between the two dates (source and target) is greater than the given maximum difference
        if ($minutes_between > $minutes_allowed) {
            $this->appendToMessageBag(['The difference between :attribute field and '.$this->constraints['compared_with'].' field is greater than the stated maximum range of '.$this->constraints['max_difference']]);
        }

        // Check if message bag is empty
        return empty($this->getFromMessageBag())? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->getFromMessageBag();
    }
}
