<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Handle flutter wave application programming interface
 */
class FlutterWaveClient
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
     * Set a secret key for flutter wave application programming interface
     * @param string $secret_key
     * @return FlutterWaveClient
     */
    public function setSecretKey(string $secret_key): FlutterWaveClient
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * Set a public key for flutter wave application programming interface
     * @param string $public_key
     * @return FlutterWaveClient
     */
    public function setPublicKey(string $public_key): FlutterWaveClient
    {
        $this->public_key = $public_key;
        return $this;
    }

    /**
     * Set payment verification criteria
     * @param string|null $id
     * @param float|null $amount
     * @param string|null $currency
     * @return FlutterWaveClient
     */
    public function setPaymentVerificationCriteria(?string $id, ?float $amount, ?string $currency): FlutterWaveClient
    {
        $this->payment_verification_criteria = [
            'tx_ref' => $id,
            'amount' => $amount,
            'currency' => $currency,
        ];
        return $this;
    }

    /**
     * Find transactions made on flutter wave and verify
     * @param string $url
     * @param string $id
     * @return FlutterWaveClient
     */
    public function verifyPayment(string $url, string $id) : FlutterWaveClient
    {
        try {

            // Parse url
            $url = Str::replaceFirst(':reference',$id,$url);

            // Query flutter wave
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