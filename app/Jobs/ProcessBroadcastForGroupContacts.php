<?php

namespace App\Jobs;

use App\Helpers\DiscordSuite;
use App\Models\Broadcast;
use App\Models\BroadcastOutgoing;
use App\Models\Group;
use App\Jobs\ProcessBroadcastOutgoing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProcessBroadcastForGroupContacts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The broadcast model.
     *
     * @var App\Models\Broadcast
     */
    protected $broadcast;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Broadcast $broadcast)
    {
        $this->broadcast = $broadcast;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Find all contacts on group
        $group = Group::with('contacts')->find($this->broadcast->contact_group_id);

        // Generate batch token
        $batch_token = uniqid();

        // Chunk the contact by defined number in user setting
        collect($group->contacts)->chunk($this->broadcast->messages_before_pause)->map(function($chunk,$batch) use ($batch_token) {

            $broadcast_outgoing = $chunk->map(function($contact) use ($batch_token,$batch){

                return [
                    'user_id' => $this->broadcast->user_id,
                    'account_id' => $this->broadcast->account_id,
                    'broadcast_id' => $this->broadcast->id,
                    'reference' => (string) Str::uuid(),
                    'contact_id' => $contact->id,
                    'batch' => $batch_token.$batch,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ];
            });

            // Save outgoing broadcasts
            DB::table((new BroadcastOutgoing)->getTable())->insert($broadcast_outgoing->toArray());

            // Dispatch messenger
            $minutes_before_resume = Carbon::parse($this->broadcast->minutes_before_resume)->minute;
            ProcessBroadcastOutgoing::dispatch($broadcast_outgoing)->delay(now()->addMinutes($minutes_before_resume));
        });
    }

    /**
     * Handle a job failure.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed($exception)
    {
        if (config('ov.api_exception_report', false)) {

            // Send user notification of failure, etc...
            DiscordSuite::messenger()->setWebhook(config('ov.discord_webhook_url'))->sendWebhook([
                'Message' => $exception->getMessage(),
                'File' => $exception->getFile(),
                'Line' => $exception->getLine(),
                'Code' => $exception->getCode(),
                'Location' => 'ProcessBroadcastForGroupContacts',
            ]);
        }
    }
}
