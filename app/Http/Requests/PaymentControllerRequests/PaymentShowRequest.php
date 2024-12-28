<?php

namespace App\Http\Requests\PaymentControllerRequests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * Check if the user is logged in and is part of a team through group
         */
        $group = auth()->user()->group($this->header('Team'));

        // Find the supplied payment by id
        $this->payment = Payment::find($this->input('id'));

        if ($this->payment) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of show_payment
             */
            if (auth()->user()->isAbleTo('show_payment', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the found payment
             */
            if (auth()->user()->id === $this->payment->user_id) {
                return true;
            }

            return false;

        } else {

            // Return failure
            throw new NotFoundHttpException();
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'properties' => 'nullable|boolean',
            'id' => 'required|uuid|max:100|min:1',
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
            'properties.boolean'  => 'Properties characters are not valid, Boolean is expected or leave as null',

            'id.required' => 'An id is required',
            'id.uuid'  => 'Id characters are not valid, UUID is expected',
            'id.max'  => 'Id characters can not be more than 100',
            'id.min'  => 'Id characters can not be less than 1',
        ];
    }
}
