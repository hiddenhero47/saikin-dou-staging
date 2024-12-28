<?php

namespace App\Jobs;

use App\Helpers\DiscordSuite;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessContactImportForGoogle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The request contacts.
     *
     * @var object
     */
    protected $contacts;

    /**
     * The request user
     *
     * @var App\Model\User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param App\Model\User
     * @param array $contacts
     * @return void
     */
    public function __construct($user, $contacts)
    {
        $this->user = $user;
        $this->contacts = $contacts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        collect($this->contacts)->chunk(30)->map(function($chunk) {

            return $chunk->map(function($item) {

                // Fill the contact model
                $contact = new Contact;
                $contact = $contact->fill([
                    'title' => $item['names'][0]['honorificPrefix'],
                    'first_name' =>  $item['names'][0]['givenName'],
                    'last_name' =>  $item['names'][0]['familyName'],
                    'email' =>  $item['emailAddresses'][0]['value'],
                    'phone' =>  $item['phoneNumbers'][0]['value'],
                ]);

                // Added user
                $contact->user_id = $this->user->id;

                // Save contact
                $contact->save();
            });
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
                'Location' => 'ProcessContactImportForGoogle',
            ]);
        }
    }
}
