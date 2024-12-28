<?php

namespace App\Http\Requests\BrowserControllerRequests;

use App\Models\Browser;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrowserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Find the supplied browser by id
        $this->browser = Browser::find($this->input('id'));

        if ($this->browser) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of update_browser
             */
            if (auth()->user()->isAbleTo('update_browser', 'administrator')) {
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
            'user_id' => 'sometimes|required|uuid|max:100|min:1',
            'account_id' => 'sometimes|required|integer|min:1|exists:accounts,id,user_id,'.$this->input('user_id'),
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
            'id.required' => 'A browser id is required',
            'id.integer'  => 'Browser id characters are not valid, Integer is expected',
            'id.min'  => 'Browser id characters can not be less than 1',

            'user_id.sometimes' => 'A user id field should be present, else entirely exclude the field',
            'user_id.required' => 'A user id maybe required',
            'user_id.uuid'  => 'User id characters are not valid, UUID is expected',
            'user_id.max'  => 'User id characters can not be more than 100',
            'user_id.min'  => 'User id characters can not be less than 1',

            'account_id.sometimes' => 'An account id field should be present, else entirely exclude the field',
            'account_id.required' => 'An account id maybe required',
            'account_id.integer'  => 'Account id characters are not valid, Integer is expected',
            'account_id.min'  => 'Account id can not be less than 1',
            'account_id.exists'  => 'Account does not exist or does not match the user id',
        ];
    }
}
