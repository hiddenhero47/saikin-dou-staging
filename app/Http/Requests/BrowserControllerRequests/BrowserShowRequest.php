<?php

namespace App\Http\Requests\BrowserControllerRequests;

use App\Models\Browser;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BrowserShowRequest extends FormRequest
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
             * Check if the user is an administrator with permission of show_browser
             */
            if (auth()->user()->isAbleTo('show_browser', 'administrator')){
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
