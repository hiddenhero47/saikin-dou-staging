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

class ProcessPaymentVerificationForPayStack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The payment id.
     *
     * @var string
     */
    protected $id;

    /**
     * Create a new job instance.
     *
     * @param string $id
     * @return void
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Find the payment on application
        $payment = Payment::where(function($query) {
            $query->where('status',config('constants.payment.status.pending'))
            ->orWhere('status',config('constants.payment.status.failure'));
        })->find($this->id);

        // Exit the process
        if (!$payment){ return; }

        // Verify payment
        $PayStackSuite = PayStackSuite::client()
        ->setSecretKey(config('ov.pay_stack_secret_key'))
        ->setPaymentVerificationCriteria($payment->id, $payment->amount, $payment->currency)
        ->verifyPayment(config('ov.pay_stack_client_url_payment_verification'),$this->id);

        // Update payment for successful payments
        if ($PayStackSuite->authentic() && $PayStackSuite->response()->data->status === 'success'){

            $payment->paid = true;
            $payment->confirmed = true;
            $payment->status = config('constants.payment.status.success');
            $payment->method = config('constants.payment.provider.paystack');
            $payment->details = json_encode($PayStackSuite->response());
            $payment->reference = $PayStackSuite->response()->data->id;
            $payment->type = $PayStackSuite->response()->data->metadata->payment_type ?? config('constants.payment.type.standard');

            // Update the payment model
            return $payment->update();
        }

        // Update payment for canceled payments
        if ($PayStackSuite->authentic() && $PayStackSuite->response()->data->status === 'abandoned'){

            $payment->paid = false;
            $payment->confirmed = false;
            $payment->status = config('constants.payment.status.failure');
            $payment->method = config('constants.payment.provider.paystack');
            $payment->details = json_encode($PayStackSuite->response());
            $payment->reference = $PayStackSuite->response()->data->id ?? null;
            $payment->type = $PayStackSuite->response()->data->metadata->payment_type ?? config('constants.payment.type.standard');

            // Update the payment model
            return $payment->update();
        }

        // Update payment for failed payments
        if (!$PayStackSuite->authentic() || $PayStackSuite->response()->data->status === 'failed'){

            $payment->paid = false;
            $payment->confirmed = false;
            $payment->status = config('constants.payment.status.failure');
            $payment->method = config('constants.payment.provider.paystack');
            $payment->details = json_encode($PayStackSuite->response());
            $payment->reference = $PayStackSuite->response()->data->id ?? null;
            $payment->type = $PayStackSuite->response()->data->metadata->payment_type ?? config('constants.payment.type.standard');

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
                'Location' => 'ProcessPaymentVerificationForPayStack',
            ]);
        }
    }
}
