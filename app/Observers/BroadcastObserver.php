<?php

namespace App\Observers;

use App\Models\Broadcast;
use App\Jobs\ProcessBroadcastForGroupContacts;
use App\Jobs\ProcessBroadcastForWhatsAppGroups;

class BroadcastObserver
{
    /**
     * Handle the broadcast "created" event.
     *
     * @param  \App\Models\Broadcast  $broadcast
     * @return void
     */
    public function created(Broadcast $broadcast)
    {
        if (!empty($broadcast->contact_group_id)) {

            ProcessBroadcastForGroupContacts::dispatch($broadcast);
        }

        if (!empty($broadcast->whatsapp_group_names)) {

            ProcessBroadcastForWhatsAppGroups::dispatch($broadcast);
        }
    }

    /**
     * Handle the broadcast "updated" event.
     *
     * @param  \App\Models\Broadcast  $broadcast
     * @return void
     */
    public function updated(Broadcast $broadcast)
    {
        if (!empty($broadcast->contact_group_id)) {

            ProcessBroadcastForGroupContacts::dispatch($broadcast);
        }

        if (!empty($broadcast->whatsapp_group_names)) {

            ProcessBroadcastForWhatsAppGroups::dispatch($broadcast);
        }
    }

    /**
     * Handle the broadcast "deleted" event.
     *
     * @param  \App\Models\Broadcast  $broadcast
     * @return void
     */
    public function deleted(Broadcast $broadcast)
    {
        //
    }

    /**
     * Handle the broadcast "restored" event.
     *
     * @param  \App\Models\Broadcast  $broadcast
     * @return void
     */
    public function restored(Broadcast $broadcast)
    {
        //
    }

    /**
     * Handle the broadcast "force deleted" event.
     *
     * @param  \App\Models\Broadcast  $broadcast
     * @return void
     */
    public function forceDeleted(Broadcast $broadcast)
    {
        //
    }
}
