<?php

namespace App\Http\Requests\PaymentControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentWebhookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->provider = $this->route()->parameter('provider');
        $providers = [
            config('constants.payment.provider.paystack'),
            config('constants.payment.provider.flutterwave'),
            config('constants.payment.provider.ogwugopay')
        ];

        if (in_array($this->provider, $providers, true)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
