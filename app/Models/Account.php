<?php

namespace App\Models;

use App\Casts\BaseUrlCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'picture',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at', 'deleted_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'picture' => BaseUrlCast::class,
        'verified' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_by = auth()->user()? auth()->user()->id : null;
        });

        self::updating(function ($model) {
            $model->updated_by = auth()->user()->id;
        });

        self::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();
        });
    }

    /**
     * Establishes a belongs to relationship with users table
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Establishes a one to one relationship with browser table
     */
    public function browser()
    {
        return $this->hasOne(Browser::class);
    }
}
