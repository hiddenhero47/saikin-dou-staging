<?php

namespace App\Http\Requests\PaymentControllerRequests;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentUpdateRequest extends FormRequest
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
             * Check if the user is an administrator with permission of update_payment
             */
            if (auth()->user()->isAbleTo('update_payment', 'administrator')){
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
            'id' => 'required|uuid|max:100|min:1',
            'user_id' => 'sometimes|required|uuid|max:100|min:1|exists:users,id',
            'type' => 'sometimes|required|in:standard,collect',
            'amount' => 'sometimes|required|integer|min:0',
            'currency' => 'sometimes|required|string|max:4|min:1',
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
            'id.required' => 'An id is required',
            'id.uuid'  => 'Id characters are not valid, UUID is expected',
            'id.max'  => 'Id characters can not be more than 100',
            'id.min'  => 'Id characters can not be less than 1',

            'user_id.sometimes' => 'A user id field should be present, else entirely exclude the field',
            'user_id.required' => 'A user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',
            'user_id.exists' => 'User id does not exist',

            'type.sometimes' => 'A payment type field should be present, else entirely exclude the field',
            'type.required' => 'Payment type maybe required',
            'type.in'  => 'Payment type field characters are not valid, string of standard or collect is expected',

            'amount.sometimes' => 'A payment amount field should be present, else entirely exclude the field',
            'amount.required' => 'A payment amount field maybe required',
            'amount.integer'  => 'Payment amount field characters are not valid, Integer is expected',
            'amount.min'  => 'Payment amount field can not be less than 0',

            'currency.sometimes' => 'A payment currency field should be present, else entirely exclude the field',
            'currency.required' => 'A payment currency field maybe required',
            'currency.string'  => 'Payment currency field characters are not valid',
            'currency.max'  => 'Payment currency field characters can not be more than 4',
            'currency.min'  => 'Payment currency field characters can not be less than 1',
        ];
    }
}
