<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class BaseUrlArrayCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, $key, $value, $attributes)
    {
        $value = json_decode($value, true);

        return collect($value)->map(function ($item) {
            return url(str_replace('public','storage',$item));
        })->all();
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value);
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
