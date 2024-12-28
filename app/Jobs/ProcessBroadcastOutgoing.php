<?php

namespace App\Jobs;

use App\Helpers\DiscordSuite;
use App\Models\Account;
use App\Models\Broadcast;
use App\Models\Contact;
use App\Models\BroadcastOutgoing;
use App\WhatsApp\WhatsAppLogin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProcessBroadcastOutgoing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     /**
     * The broadcast model.
     *
     * @var Illuminate\Support\Collection
     */
    protected $broadcast_outgoing;

    /**
     * Create a new job instance.
     *
     * @param Illuminate\Support\Collection $broadcast_outgoing
     * @return void
     */
    public function __construct($broadcast_outgoing)
    {
        $this->broadcast_outgoing = $broadcast_outgoing;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        return $this->broadcast_outgoing->map(function($item){

            // Retrieve required details
            $account = Account::with('browser')->find($item['account_id']);
            $broadcast = Broadcast::find($item['broadcast_id']);

            // Dispatch
            if ($account && $account->browser && $broadcast) {

                try {

                    // Deploy browser
                    $WhatsAppLogin = new WhatsAppLogin();
                    $WhatsAppLogin = $WhatsAppLogin->continueBrowserSession($account->browser->session_id);

                    // Send Messages
                    if (isset($item['contact_id'])) {
                        $contact = Contact::find($item['contact_id']);
                        $WhatsAppLogin->setTextMessage($broadcast->message)->setRecipients([$contact->phone])->sendMessageToContacts();
                    }

                    if (isset($item['whatsapp_group_name'])) {
                        $WhatsAppLogin->setTextMessage($broadcast->message);
                    }

                    // Save browser
                    Browser::where('id',$account->browser->id)->update([
                        'session_id' => $WhatsAppLogin->getBrowserSessionId(),
                        'browser_instance' => $WhatsAppLogin->getBrowserInstance(),
                    ]);

                    // Mark outgoing broadcast as delivered
                    BroadcastOutgoing::where('reference',$item['reference'])->update([
                        'status'=>config('constants.status.delivered')
                    ]);

                } catch (\Throwable $th) {

                    // Mark outgoing broadcast as delivered
                    BroadcastOutgoing::where('reference',$item['reference'])->update([
                        'status'=>config('constants.status.canceled'),
                        'exception' => serialize($th->getMessage())
                    ]);
                }
            }
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
                'Location' => 'ProcessBroadcastOutgoing',
            ]);
        }
    }
}
