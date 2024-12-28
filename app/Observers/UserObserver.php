<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Setting;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // Create a user profile
        $profile = new Setting;
        $profile->user_id = $user->id;
        $profile->save();
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        // Delete a user profile
        Setting::where('user_id',$user->id)->delete();
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        // Restore a user profile
        Setting::where('user_id',$user->id)->restore();
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        // Force delete a user profile
        Setting::where('user_id',$user->id)->forceDelete();
    }
}
