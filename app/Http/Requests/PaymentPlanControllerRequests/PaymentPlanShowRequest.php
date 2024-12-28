<?php

namespace App\Http\Requests\PaymentPlanControllerRequests;

use App\Models\PaymentPlan;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentPlanShowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Find the supplied food by id
        $this->payment_plan = PaymentPlan::find($this->input('id'));

        if ($this->payment_plan) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of show_payment_plan
             */
            if (auth()->user()->isAbleTo('show_payment_plan', 'administrator')){
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
            'id' => 'required|integer|min:1',
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
            'id.integer'  => 'Id characters are not valid, Integer is expected',
            'id.min'  => 'Id characters can not be less than 1',
        ];
    }
}
