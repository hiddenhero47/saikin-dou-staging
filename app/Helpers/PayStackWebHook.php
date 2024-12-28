<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Handle pay stack webhook response
 */
class PayStackWebHook
{
    /**
     * @var array $whitelisted_ip
     */
    protected array $whitelisted_ip = [
        '52.31.139.75',
        '52.49.173.169',
        '52.214.14.220',
        '127.0.0.1',
    ];

    /**
     * @var array $event_types
     */
    protected array $event_types = [
        'customeridentification.failed',
        'customeridentification.success',
        'charge.dispute.create',
        'charge.dispute.remind',
        'charge.dispute.resolve',
        'dedicatedaccount.assign.failed',
        'dedicatedaccount.assign.success',
        'invoice.create',
        'invoice.payment_failed',
        'invoice.update',
        'paymentrequest.pending',
        'paymentrequest.success',
        'refund.failed',
        'refund.pending',
        'refund.processed',
        'refund.processing',
        'subscription.create',
        'subscription.disable',
        'subscription.not_renew',
        'subscription.expiring_cards',
        'charge.success',
        'transfer.success',
        'transfer.failed',
        'transfer.reversed'
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
     * @return PayStackWebHook
     */
    public function setRequestHeader(array $header) : PayStackWebHook 
    {
        $this->header = $header;
        return $this;
    }

    /**
     * Set the received webhook body
     * @param array $body
     * @return PayStackWebHook
     */
    public function setRequestBody(array $body) : PayStackWebHook 
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Set the received webhook content
     * @param string $content
     * @return PayStackWebHook
     */
    public function setRequestContent(string $content) : PayStackWebHook 
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Set the received webhook ip
     * @param string $ip
     * @return PayStackWebHook
     */
    public function setRequestIp(string $ip) : PayStackWebHook 
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * Set a secret key for pay stack webhook
     * @param string $secret_key
     * @return PayStackWebHook
     */
    public function setSecretKey(string $secret_key): PayStackWebHook
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * Set a public key for pay stack webhook
     * @param string $public_key
     * @return PayStackWebHook
     */
    public function setPublicKey(string $public_key): PayStackWebHook
    {
        $this->public_key = $public_key;
        return $this;
    }

    /**
     * Set payment verification criteria
     * @param string|null $id
     * @param float|null $amount
     * @param string|null $currency
     * @return PayStackWebHook
     */
    public function setPaymentVerificationCriteria(?string $id, ?float $amount, ?string $currency): PayStackWebHook
    {
        $this->payment_verification_criteria = [
            'reference' => $id,
            'amount' => intval($amount * 100),
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
        if (!isset($this->header['x-paystack-signature'][0])) {
            return false;
        }

        if (!in_array($this->ip, $this->whitelisted_ip)) {
            return false;
        }

        if ($this->header['x-paystack-signature'][0] === hash_hmac('sha512', $this->content, $this->secret_key)) {
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
     * Find transactions made on pay stack and verify against received webhook
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
            $url = Str::replaceFirst(':reference',$content->data->reference,$url);

            // Query pay stack
            $response = Http::withToken($this->secret_key)->get($url)->throw();

            // Parse response
            $this->response = $response->object();

            // Compare verification criteria against response key-value
            $is_transaction_data_accurate = collect($this->payment_verification_criteria ?? [])->every(function ($value, $accessor) {
                return $value ? $value === $this->response->data->$accessor : true;
            });

            // Compare web hook key-value against response key-value
            $is_webhook_data_authentic = collect(['id','status','reference','amount','currency'])->every(function ($accessor) use ($content) {
                return $content->data->$accessor === $this->response->data->$accessor;
            });

            $this->is_authentic = $is_webhook_data_authentic && $is_transaction_data_accurate ? true : false;

        } catch (\Throwable $th) {

            $this->is_authentic = false;
        }

        return $this;
    }
}
