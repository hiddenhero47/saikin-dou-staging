<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'currency',
        'amount',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at', 
        'deleted_by',
        'details'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'double',
        'paid' => 'boolean',
        'confirmed' => 'boolean',
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Set the payment amount.
     *
     * @param double $value
     * @return void
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = (double) $value < 0 ? 0 : (double) $value;
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->id = (string) Str::uuid();
            $model->pfm = $model->generatePFM();
            $model->created_by = auth()->user()->id;
        });

        self::updating(function ($model) {
            $model->updated_by = auth()->user()? auth()->user()->id : null;
        });

        self::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();
        });
    }

    /**
     * Generate a unique pfm code
     * 
     * @param void
     * @return string
     */
    public function generatePFM()
    {
        $string = Str::random(10);
        return $this->where('pfm', $string)->exists() ? $this->generatePFM() : $string;
    }

    /**
     * Establishes a belongs to relationship with users table
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
