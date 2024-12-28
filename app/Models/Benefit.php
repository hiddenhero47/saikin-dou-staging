<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Benefit extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
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
    }

    /**
     * Scope a query to only include benefit of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsPublic($query)
    {
        return $query->where('visibility', config('constants.visibility.public'));
    }

    /**
     * Scope a query to only include benefit of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsPrivate($query)
    {
        return $query->where('visibility', config('constants.visibility.private'));
    }

    /**
     * Establishes a belongs to many relationship with accounts table
     */
    public function accounts()
    {
        return $this->belongsToMany(Account::class, PaymentPlan::class);
    }

    /**
     * Establishes a belongs to many relationship with Payment plans table
     */
    public function paymentPlans()
    {
        return $this->belongsToMany(PaymentPlan::class);
    }
}

