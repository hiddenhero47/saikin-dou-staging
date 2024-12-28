<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class GenderCast implements CastsAttributes
{
    /**
     * A codex for gender number equivalent
     */
    public $gender_code = [
        'male' => '1',
        'female' => '2',
        'trans_male' => '3',
        'trans_female' => '4',
        'unknown' => '9'
    ];

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        return array_search($value, $this->gender_code);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return $this->gender_code[strtolower(trim($value))] ?? '9';
    }

    /**
     * Evaluate what the given value would have been on get
     * 
     * @param string $value
     * @return mixed
     */
    public static function atGet(string $value)
    {
        return (new self)->get(null,null,$value,null);
    }

    /**
     * Evaluate what the given value would have been on set
     * 
     * @param string $value
     * @return mixed
     */
    public static function atSet(string $value)
    {
        return (new self)->set(null,null,$value,null);
    }
}