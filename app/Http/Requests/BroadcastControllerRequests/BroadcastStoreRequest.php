<?php

namespace App\Http\Requests\BroadcastControllerRequests;

use App\Models\Account;
use App\Rules\Maximum;
use App\Rules\Base64Image;
use App\Rules\UrlImage;
use Illuminate\Foundation\Http\FormRequest;

class BroadcastStoreRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:50|min:1',
            'preview_phone' => 'sometimes|required|numeric|digits_between:1,25',
            'message' =>'required|string|min:1',
            'contact_group_start_date' => 'sometimes|required|date|max:25|min:1',
            'contact_group_end_date' => 'sometimes|required|date|max:25|min:1',
            'contact_group_id' => 'required_without:whatsapp_group_names|integer|min:1|exists:groups,id,user_id,'.auth()->user()->id,
            'whatsapp_group_names' => 'required_without:contact_group_id|array|min:1|filled',
            'whatsapp_group_names.*' => 'required_unless:whatsapp_group_names,'.null.'|string|min:1',

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

            'title.sometimes' => 'A title should be present, else entirely exclude the field',
            'title.required' => 'A title maybe required',
            'title.string'  => 'Title characters are not valid',
            'title.max'  => 'Title characters can not be more than 50',
            'title.min'  => 'Title characters can not be less than 1',

            'preview_phone.sometimes' => 'A preview phone number should be present, else entirely exclude the field',
            'preview_phone.required' => 'A preview phone number maybe required',
            'preview_phone.numeric'  => 'Preview phone number characters are not valid',
            'preview_phone.digits_between'  => 'Preview phone number characters can not be more than 25 or less than 1',

            'message.required' => 'A message field is required',
            'message.string'  => 'Message description field characters are not valid',
            'message.min'  => 'Message description field characters can not be less than 1',

            'contact_group_start_date.sometimes' => 'A contact group start date field should be present, else entirely exclude the field',
            'contact_group_start_date.required' => 'A contact group start date field maybe required',
            'contact_group_start_date.date'  => 'Contact group start date field characters are not valid, A valid date string is expected',
            'contact_group_start_date.max'  => 'Contact group start date field characters can not be more than 25',
            'contact_group_start_date.min'  => 'Contact group start date field characters can not be less than 1',

            'contact_group_end_date.sometimes' => 'A contact group end date field should be present, else entirely exclude the field',
            'contact_group_end_date.required' => 'A contact group end date field maybe required',
            'contact_group_end_date.date'  => 'Contact group end date field characters are not valid, A valid date string is expected',
            'contact_group_end_date.max'  => 'Contact group end date field characters can not be more than 25',
            'contact_group_end_date.min'  => 'Contact group end date field characters can not be less than 1',

            'contact_group_id.required_without' => 'A contact group id is required if there is no whatsapp group name',
            'contact_group_id.string'  => 'Contact group id characters are not valid, Integer is required',
            'contact_group_id.min'  => 'Contact group id characters can not be less than 1',
            'contact_group_id.exists'  => 'Contact group id does not exist for this user',

            'whatsapp_group_names.required_without' => 'A whats app group names field is required if there is no contact group id',
            'whatsapp_group_names.array'  => 'Whats app group names field is not valid, Array is required',
            'whatsapp_group_names.min'  => 'Whats app group names field can not be less than 1',
            'whatsapp_group_names.filled'  => 'Whats app group names field must be filled',

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
