<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

/**
 * Use discord communication channels 
 */
class DiscordMessenger
{
    /**
     * @var string $webhook
     */
    protected string $webhook;

    /**
     * Create a new DiscordMessenger instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Format a message
     * @param null|string|array $message
     * @return string
     */
    public function formatMessage($message = null)
    {
        if (is_array($message)){
            return json_encode($message);
        }

        if (is_string($message)){
            return $message;
        }

        return 'No Message';
    }

    /**
     * Set a webhook for discord
     * @param string $webhook
     * @return DiscordMessenger
     */
    public function setWebhook(string $webhook)
    {
        $this->webhook = $webhook;
        return $this;
    }

    /**
     * Send a webhook through discord
     * @param null|string|array $message 
     * @return boolean
     */
    public function sendWebhook($message = null)
    {
        // Format message to string / json string
        $message = $this->formatMessage($message);

        $response = Http::post($this->webhook, [
            'content' => $message,
        ]);

        return $response->successful();
    }
}
