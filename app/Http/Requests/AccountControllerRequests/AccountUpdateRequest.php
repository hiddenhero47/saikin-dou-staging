<?php

namespace App\Http\Requests\AccountControllerRequests;

use App\Models\Account;
use App\Rules\Maximum;
use App\Rules\Base64Image;
use App\Rules\UrlImage;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccountUpdateRequest extends FormRequest
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
        $this->account = Account::find($this->input('id'));

        if ($this->account) {

            /**
             * Check if requestor is able to ...
             * Check if the user is an administrator with permission of update_account
             */
            if (auth()->user()->isAbleTo('update_account', 'administrator')){
                return true;
            }

            /**
             * Check if requestor is able to ...
             * Check if the user is related to the found account
             */
            if ($this->account->user_id === auth()->user()->id){
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
            'phone' => 'sometimes|required|numeric|digits_between:1,25',
            'first_name' => 'sometimes|required|string|max:50|min:1',
            'last_name' => 'sometimes|required|string|max:50|min:1',

            'photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'base64_photos','url_photos')],
            'photos.*' => 'required_unless:photos,'.null.'|image|dimensions:min_width=200,min_height=200|mimes:jpg,jpeg,png,gif,bmp|max:1999',

            'base64_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'photos','url_photos')],
            'base64_photos.*' => ['required_unless:base64_photos,'.null, new Base64Image(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'url_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'photos','base64_photos')],
            'url_photos.*' => ['required_unless:url_photos,'.null, new UrlImage(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],
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
            'id.min'  => 'Id can not be less than 1',

            'phone.sometimes' => 'A phone number should be present, else entirely exclude the field',
            'phone.required' => 'A phone number maybe required',
            'phone.numeric' => 'Phone number characters are not valid',
            'phone.digits_between' => 'A phone number can not have more than 25 characters or less than 1',

            'first_name.sometimes' => 'A first name should be present, else entirely exclude the field',
            'first_name.required' => 'A first name maybe required',
            'first_name.string'  => 'First name characters are not valid',
            'first_name.max'  => 'First name characters can not be more than 50',
            'first_name.min'  => 'First name characters can not be less than 1',

            'last_name.sometimes' => 'A last name should be present, else entirely exclude the field',
            'last_name.required' => 'A last name maybe required',
            'last_name.string'  => 'Last name characters are not valid',
            'last_name.max'  => 'Last name characters can not be more than 50',
            'last_name.min'  => 'Last name characters can not be less than 1',

            'photos.sometimes' => 'An array of photos should be present, else entirely exclude the field',
            'photos.required' => 'An array photos maybe required',
            'photos.array' => 'The photos field is not valid, Array is expected',
            'photos.max' => 'Photos array can contain only one(1) photo',
            'photos.filled' => 'Photos array can not be empty',

            'photos.*.image' => 'Must be an image',
            'photos.*.dimensions' => 'Image dimensions are a minimum width:200 and a minimum height:200',
            'photos.*.max' => 'Image size must be less than 2MB',
            'photos.*' => 'Only image files are allowed in photos field',

            'base64_photos.sometimes' => 'An array of base64 photos should be present, else entirely exclude the field',
            'base64_photos.required' => 'An array base64 photos maybe required',
            'base64_photos.array' => 'The base64 photos field is not valid, Array is expected',
            'base64_photos.max' => 'Base64 photos array can contain only one(1) photo',
            'base64_photos.filled' => 'Base64 photos array can not be empty',

            'url_photos.sometimes' => 'An array of url photos should be present, else entirely exclude the field',
            'url_photos.required' => 'An array of url photos maybe required',
            'url_photos.array' => 'The url photos field is not valid, Array is expected',
            'url_photos.max' => 'Url photos array can contain only one(1) photo',
            'url_photos.filled' => 'Url photos array can not be empty',
        ];
    }
}
