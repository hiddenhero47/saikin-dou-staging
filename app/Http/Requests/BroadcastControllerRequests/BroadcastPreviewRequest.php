<?php

namespace App\Http\Requests\BroadcastControllerRequests;

use App\Models\Account;
use App\Rules\Maximum;
use App\Rules\Base64Image;
use App\Rules\UrlImage;
use Illuminate\Foundation\Http\FormRequest;

class BroadcastPreviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_id' => 'required|integer|min:1|exists:accounts,id,user_id,'.auth()->user()->id,
            'preview_phone' => 'sometimes|required|numeric|digits_between:1,25',
            'message' =>'required|string|min:1',

            'photos' => ['sometimes','required', 'array', 'max:9', 'filled', new Maximum(9,'base64_photos','url_photos')],
            'photos.*' => 'required_unless:photos,'.null.'|image|dimensions:min_width=200,min_height=200|mimes:jpg,jpeg,png,gif,bmp|max:1999',

            'base64_photos' => ['sometimes','required', 'array', 'max:9', 'filled', new Maximum(9,'photos','url_photos')],
            'base64_photos.*' => ['required_unless:base64_photos,'.null, new Base64Image(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'url_photos' => ['sometimes','required', 'array', 'max:9', 'filled', new Maximum(9,'photos','base64_photos')],
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
            'account_id.required' => 'An account id is required',
            'account_id.string'  => 'Account id characters are not valid, Integer is required',
            'account_id.min'  => 'Account id characters can not be less than 1',
            'account_id.exists'  => 'Account id does not exist for this user',

            'preview_phone.sometimes' => 'A preview phone number should be present, else entirely exclude the field',
            'preview_phone.required' => 'A preview phone number maybe required',
            'preview_phone.numeric'  => 'Preview phone number characters are not valid',
            'preview_phone.digits_between'  => 'Preview phone number characters can not be more than 25 or less than 1',

            'message.required' => 'A message field is required',
            'message.string'  => 'Message description field characters are not valid',
            'message.min'  => 'Message description field characters can not be less than 1',

            'photos.sometimes' => 'An array of photos should be present, else entirely exclude the field',
            'photos.required' => 'An array photos maybe required',
            'photos.array' => 'The photos field is not valid, Array is expected',
            'photos.max' => 'Photos array can contain only nine(9) photos',
            'photos.filled' => 'Photos array can not be empty',

            'photos.*.image' => 'Must be an image',
            'photos.*.dimensions' => 'Image dimensions are a minimum width:200 and a minimum height:200',
            'photos.*.max' => 'Image size must be less than 2MB',
            'photos.*' => 'Only image files are allowed in photos field',

            'base64_photos.sometimes' => 'An array of base64 photos should be present, else entirely exclude the field',
            'base64_photos.required' => 'An array base64 photos maybe required',
            'base64_photos.array' => 'The base64 photos field is not valid, Array is expected',
            'base64_photos.max' => 'Base64 photos array can contain only nine(9) photos',
            'base64_photos.filled' => 'Base64 photos array can not be empty',

            'url_photos.sometimes' => 'An array of url photos should be present, else entirely exclude the field',
            'url_photos.required' => 'An array of url photos maybe required',
            'url_photos.array' => 'The url photos field is not valid, Array is expected',
            'url_photos.max' => 'Url photos array can contain only nine(9) photos',
            'url_photos.filled' => 'Url photos array can not be empty',
        ];
    }
}
