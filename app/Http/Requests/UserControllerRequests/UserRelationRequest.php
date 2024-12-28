<?php

namespace App\Http\Requests\UserControllerRequests;

use Illuminate\Foundation\Http\FormRequest;

class UserRelationRequest extends FormRequest
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

        /**
         * Check if requestor is able to ...
         * Check if the user is an administrator with permission of relation_user
         * Check if the user is related to the found user
         */
        if (auth()->user()->isAbleTo('relation_user', 'administrator')){
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
            'address' => 'nullable|boolean',
            'business' => 'nullable|boolean',
            'employee' => 'nullable|boolean',
            'human_verification' => 'nullable|boolean',
            'profile' => 'nullable|boolean',
            'restaurant_orders' => 'nullable|boolean',
            'roles' => 'nullable|boolean',
            'store_orders' => 'nullable|boolean',
            'subscription_food_orders' => 'nullable|boolean',
            'subscription_merchandise_orders' => 'nullable|boolean',
            'wallet' => 'nullable|boolean',
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
            'address.boolean'  => 'Address characters are not valid, Boolean is expected or leave as null',
            'business.boolean'  => 'Business characters are not valid, Boolean is expected or leave as null',
            'employee.boolean'  => 'Employee characters are not valid, Boolean is expected or leave as null',
            'human_verification.boolean'  => 'Human verification characters are not valid, Boolean is expected or leave as null',
            'profile.boolean'  => 'Profile characters are not valid, Boolean is expected or leave as null',
            'restaurant_orders.boolean'  => 'Restaurant orders characters are not valid, Boolean is expected or leave as null',
            'roles.boolean'  => 'Roles characters are not valid, Boolean is expected or leave as null',
            'store_orders.boolean'  => 'Store orders characters are not valid, Boolean is expected or leave as null',
            'subscription_food_orders.boolean'  => 'Subscription food orders characters are not valid, Boolean is expected or leave as null',
            'subscription_merchandise_orders.boolean'  => 'Subscription merchandise orders characters are not valid, Boolean is expected or leave as null',
            'wallet.boolean'  => 'Wallet characters are not valid, Boolean is expected or leave as null',
        ];
    }
}
