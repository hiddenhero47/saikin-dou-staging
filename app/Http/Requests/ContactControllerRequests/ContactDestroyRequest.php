<?php

namespace App\Http\Requests\ContactControllerRequests;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactDestroyRequest extends FormRequest
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

        // Find the supplied user by id
        $this->contact = Contact::find($this->input('id'));

        if ($this->contact) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of delete_contact
             */
            if (auth()->user()->isAbleTo('delete_contact', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the found contact
             */
            if (auth()->user()->id === $this->contact->user_id) {
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
            'id.required' => 'An id is required',
            'id.integer'  => 'Id characters are not valid, Integer is expected',
            'id.min'  => 'Id characters can not be less than 1',
        ];
    }
}
