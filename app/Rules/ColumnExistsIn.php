<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Schema;

/**
 * The file under validation must be a string 
 * having matched the given constrains. 
 * 
 * $request->validate([ 'field' => [new ColumnExistsIn('table')] ]);
 */
class ColumnExistsIn implements Rule
{
    /**
     * Table
     *
     * @var string
     */
    protected string $table;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $table)
    {
        $this->table = $table;
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
        return Schema::hasColumn($this->table, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The column :input does not exist in the :attribute table.';
    }
}
