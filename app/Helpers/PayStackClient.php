<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Handle pay stack application programming interface
 */
class PayStackClient
{
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
     * @return object
     */
    public function response() 
    {
        return $this->response ?? null;
    }

    /**
     * Return is_authentic
     * @param void
     * @return bool
     */
    public function authentic() 
    {
        return $this->is_authentic ?? null;
    }

    /**
     * Set a secret key for pay stack application programming interface
     * @param string $secret_key
     * @return PayStackClient
     */
    public function setSecretKey(string $secret_key): PayStackClient
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * Set a public key for pay stack application programming interface
     * @param string $public_key
     * @return PayStackClient
     */
    public function setPublicKey(string $public_key): PayStackClient
    {
        $this->public_key = $public_key;
        return $this;
    }

    /**
     * Set payment verification criteria
     * @param string|null $id
     * @param float|null $amount
     * @param string|null $currency
     * @return PayStackClient
     */
    public function setPaymentVerificationCriteria(?string $id, ?float $amount, ?string $currency): PayStackClient
    {
        $this->payment_verification_criteria = [
            'reference' => $id,
            'amount' => intval($amount * 100),
            'currency' => $currency,
        ];
        return $this;
    }

    /**
     * Find transactions made on pay stack and verify
     * @param string $url
     * @param string $id
     * @return PayStackClient
     */
    public function verifyPayment(string $url, string $id) : PayStackClient
    {
        try {

            // Parse url
            $url = Str::replaceFirst(':reference',$id,$url);

            // Query pay stack
            $response = Http::withToken($this->secret_key)->get($url)->throw();

            // Parse response
            $this->response = $response->object();

            // Compare verification criteria against response key-value
            $is_transaction_data_accurate = collect($this->payment_verification_criteria ?? [])->every(function ($value, $accessor) {
                return $value ? $value === $this->response->data->$accessor : true;
            });

            $this->is_authentic = $is_transaction_data_accurate;

        } catch (\Throwable $th) {

            $this->is_authentic = false;
        }

        return $this;
    }
}