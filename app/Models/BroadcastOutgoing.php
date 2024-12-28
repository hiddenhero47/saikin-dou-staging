<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class BroadcastOutgoing extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'broadcast_id',
        'account_id',
        'contact_id',
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
     * Establishes a belongs to relationship with users table
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Establishes a belongs to relationship with broadcasts table
     */
    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }

    /**
     * Establishes a belongs to relationship with accounts table
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Establishes a belongs to relationship with contacts table
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
