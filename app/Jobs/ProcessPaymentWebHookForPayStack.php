<?php

namespace App\Jobs;

use App\Helpers\DiscordSuite;
use App\Helpers\PayStackSuite;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPaymentWebHookForPayStack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The request content.
     *
     * @var string
     */
    protected $content;

    /**
     * The request content but parsed.
     *
     * @var array
     */
    protected $parsed_content;

    /**
     * Create a new job instance.
     *
     * @param string $content
     * @return void
     */
    public function __construct(string $content)
    {
        $this->content = $content;
        $this->parsed_content = json_decode($content,false);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Find the payment model
        $payment = Payment::where(function($query) {
            $query->where('status',config('constants.payment.status.pending'))
            ->orWhere('status',config('constants.payment.status.failure'));
        })->find($this->parsed_content->data->reference);

        // Exit the process
        if (!$payment){ return; }

        // Verify payment
        $PayStackSuite = PayStackSuite::webhook()
        ->setSecretKey(config('ov.pay_stack_secret_key'))
        ->setPaymentVerificationCriteria($payment->id, $payment->amount, $payment->currency)
        ->verifyPayment(config('ov.pay_stack_client_url_payment_verification'),$this->content);

        // Update payment for successful payments with event type of 'charge.success'
        if ($PayStackSuite->authentic() && $PayStackSuite->response()->data->status === 'success' && $this->parsed_content->event === 'charge.success'){

            $payment->paid = true;
            $payment->confirmed = true;
            $payment->status = config('constants.payment.status.success');
            $payment->method = config('constants.payment.provider.paystack');
            $payment->details = $this->content;
            $payment->reference = $this->parsed_content->data->id;
            $payment->type = $authentic->data->metadata->payment_type ?? config('constants.payment.type.standard');

            // Update the payment model
            return $payment->update();
        }

        // Update payment for failed payments
        if (!$PayStackSuite->authentic() || $PayStackSuite->response()->data->status === 'failed'){

            $payment->paid = false;
            $payment->confirmed = false;
            $payment->status = config('constants.payment.status.failure');
            $payment->method = config('constants.payment.provider.paystack');
            $payment->details = $this->content;
            $payment->reference = $this->parsed_content->data->id;
            $payment->type = $authentic->data->metadata->payment_type ?? config('constants.payment.type.standard');

            // Update the payment model
            return $payment->update();
        }
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
                'Location' => 'ProcessPaymentWebHookForPayStack',
                'log' =>  $this->content
            ]);
        }
    }
}
