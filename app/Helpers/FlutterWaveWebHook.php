<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Handle flutter wave webhook response
 */
class FlutterWaveWebHook
{
    /**
     * @var array $event_types
     */
    protected array $event_types = [
        'charge.completed',
        'transfer.completed',
        'subscription.cancelled',
    ];

    /**
     * @var array $header
     */
    protected array $header;

    /**
     * @var array $body
     */
    protected array $body;

    /**
     * @var string $content
     */
    protected string $content;

    /**
     * @var string $ip
     */
    protected string $ip;

    /**
     * @var string $secret_key
     */
    protected string $secret_key;

    /**
     * @var string $public_key
     */
    protected string $public_key;

    /**
     * @var string $verification_hash
     */
    protected string $verification_hash;

    /**
     * @var array $payment_verification_criteria
     */
    protected array $payment_verification_criteria;

    /**
     * @var bool $is_authentic
     */
    protected bool $is_authentic;

    /**
     * @var object $response
     */
    protected object $response;

    /**
     * Return response
     * @param void
     * @return object|null
     */
    public function response() 
    {
        return $this->response ?? null;
    }

    /**
     * Return is_authentic
     * @param void
     * @return bool|null
     */
    public function authentic() 
    {
        return $this->is_authentic ?? null;
    }

    /**
     * Set the received webhook header
     * @param array $header
     * @return FlutterWaveWebHook
     */
    public function setRequestHeader(array $header) : FlutterWaveWebHook 
    {
        $this->header = $header;
        return $this;
    }

    /**
     * Set the received webhook body
     * @param array $body
     * @return FlutterWaveWebHook
     */
    public function setRequestBody(array $body) : FlutterWaveWebHook 
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Set the received webhook content
     * @param string $content
     * @return FlutterWaveWebHook
     */
    public function setRequestContent(string $content) : FlutterWaveWebHook 
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the received webhook ip
     * @param string $ip
     * @return FlutterWaveWebHook
     */
    public function setRequestIp(string $ip) : FlutterWaveWebHook 
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Set a secret key for flutter wave webhook
     * @param string $secret_key
     * @return PayStackWebHook
     */
    public function setSecretKey(string $secret_key): FlutterWaveWebHook
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * Set a public key for flutter wave webhook
     * @param string $public_key
     * @return PayStackWebHook
     */
    public function setPublicKey(string $public_key): FlutterWaveWebHook
    {
        $this->public_key = $public_key;
        return $this;
    }

    /**
     * Set a verification hash for flutter wave webhook
     * @param string $verification_hash
     * @return FlutterWaveWebHook
     */
    public function setVerificationHash(string $verification_hash): FlutterWaveWebHook
    {
        $this->verification_hash = $verification_hash;
        return $this;
    }

    /**
     * Set payment verification criteria
     * @param string|null $id
     * @param float|null $amount
     * @param string|null $currency
     * @return FlutterWaveWebHook
     */
    public function setPaymentVerificationCriteria(?string $id, ?float $amount, ?string $currency): FlutterWaveWebHook
    {
        $this->payment_verification_criteria = [
            'tx_ref' => $id,
            'amount' => $amount,
            'currency' => $currency,
        ];
        return $this;
    }

    /**
     * Check if the webhook is authorized
     * @param void
     * @return bool
     */
    public function isAuthorized(): bool
    {
        if (!isset($this->header['verif-hash'][0])) {
            return false;
        }

        if ($this->header['verif-hash'][0] ===  $this->verification_hash) {
            return true;
        }

        return false;
    }

    /**
     * Check if the webhook has the given event type
     * @param string type
     * @return bool
     */
    public function isEventType(string $type): bool
    {
        return in_array($type, $this->event_types) && $this->body['event'] === $type;
    }

    /**
     * Check if the webhook has any of the given event types
     * @param array types
     * @return bool
     */
    public function isEventTypeAnyOf(array $types): bool
    {
        return array_intersect($this->event_types, $types) && in_array($this->body['event'], $types);
    }

    /**
     * Find transactions made on flutter wave and verify against received webhook
     * @param string $url
     * @param string $content
     * @return array|false
     */
    public function verifyPayment(string $url, string $content)
    {
        try {

            // Parse content
            $content = json_decode($content,false);

            // Parse url
            $url = Str::replaceFirst(':reference',$content->data->tx_ref,$url);

            // Query flutter wave
            $response = Http::withToken($this->secret_key)->get($url)->throw();

            // Parse response
            $this->response = $response->object();

            // Compare verification criteria against response key-value
            $is_transaction_data_accurate = collect($this->payment_verification_criteria ?? [])->every(function ($value, $accessor) {
                return $value ? $value === $this->response->data->$accessor : true;
            });

            // Compare web hook key-value against response key-value
            $is_webhook_data_authentic = collect(['id','tx_ref','flw_ref','amount','currency','status'])->every(function ($accessor) use ($content) {
                return $content->data->$accessor === $this->response->data->$accessor;
            });

            $this->is_authentic = $is_webhook_data_authentic && $is_transaction_data_accurate ? true : false;

        } catch (\Throwable $th) {

            $this->is_authentic = false;
        }

        return $this;
    }
}
