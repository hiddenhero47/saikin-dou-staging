<?php

namespace App\Http\Requests\EmbeddedFormControllerRequests;

use App\Models\EmbeddedForm;
use App\Rules\Maximum;
use App\Rules\Base64Image;
use App\Rules\UrlImage;
use Illuminate\Foundation\Http\FormRequest;

class EmbeddedFormStoreRequest extends FormRequest
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
            'group_id' => 'required|integer|min:1',
            'title' => 'required|string|max:100',
            'custom_url' => 'sometimes|required|url|max:100',
            'description' => 'sometimes|required|string|max:16777215',

            'input_fields' => 'required|array',
            'input_fields' => 'sometimes|required|array|max:18|min:1',
            'input_fields.*' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',

            'input_fields.first_name' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.first_name.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.first_name.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.last_name' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.last_name.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.last_name.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.whatsapp_number' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.whatsapp_number.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.whatsapp_number.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.email_address' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.email_address.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.email_address.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.house_address' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.house_address.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.house_address.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'input_fields.*' => 'required_unless:input_fields,'.null.'|array|max:2|min:2',
            'input_fields.*.is_required' => 'required_unless:input_fields,'.null.'|boolean',
            'input_fields.*.is_shown' => 'required_unless:input_fields,'.null.'|boolean',

            'form_header_text' => 'sometimes|required|string|max:255|min:1',

            'form_header_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_header_base64_photos','form_header_url_photos')],
            'form_header_photos.*' => 'required_unless:form_header_photos,'.null.'|image|dimensions:min_width=200,min_height=200|mimes:jpg,jpeg,png,gif,bmp|max:1999',

            'form_header_base64_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_header_photos','form_header_url_photos')],
            'form_header_base64_photos.*' => ['required_unless:form_header_base64_photos,'.null, new Base64Image(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'form_header_url_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_header_photos','form_header_base64_photos')],
            'form_header_url_photos.*' => ['required_unless:form_header_url_photos,'.null, new UrlImage(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'form_footer_text' => 'sometimes|required|string|max:255|min:1',

            'form_footer_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_footer_base64_photos','form_footer_url_photos')],
            'form_footer_photos.*' => 'required_unless:form_footer_photos,'.null.'|image|dimensions:min_width=200,min_height=200|mimes:jpg,jpeg,png,gif,bmp|max:1999',

            'form_footer_base64_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_footer_photos','form_footer_url_photos')],
            'form_footer_base64_photos.*' => ['required_unless:form_footer_base64_photos,'.null, new Base64Image(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'form_footer_url_photos' => ['sometimes','required', 'array', 'max:1', 'filled', new Maximum(1,'form_footer_photos','form_footer_base64_photos')],
            'form_footer_url_photos.*' => ['required_unless:form_footer_url_photos,'.null, new UrlImage(['min_width'=>200,'min_height'=>200,'mimes'=>['jpg','jpeg','png','gif','bmp'],'max_size'=>1999])],

            'form_background_color' => ['required','string','regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i','max:20'],
            'form_width' => 'required|in:small,normal,large|max:30',
            'form_border_radius' => 'sometimes|required|integer|max:10|min:1',
            'submit_button_color' => ['required','string','regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i','max:20'],
            'submit_button_text' => 'sometimes|required|string|max:100|min:1',
            'submit_button_text_color' => ['required','string','regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i','max:20'],
            'submit_button_text_before' => 'sometimes|required|string|max:100|min:1',
            'submit_button_text_after' => 'sometimes|required|string|max:100|min:1',
            'thank_you_message' => 'sometimes|required|string|max:255|min:1',
            'thank_you_message_url' => 'sometimes|required|url|max:255|min:1',
            'facebook_pixel_code' => 'sometimes|required|string|max:100|min:1',
            'auto_responder_id' => 'sometimes|required|integer|min:1',
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
            'group_id.required' => 'A group id is required',
            'group_id.integer'  => 'Group id characters are not valid, Integer is expected',
            'group_id.min'  => 'Group id characters can not be less than 1',

            'title.required' => 'A title field is required',
            'title.string'  => 'Title field characters are not valid',
            'title.max'  => 'Title characters can not be more than 100',

            'custom_url.sometimes' => 'A custom url field should be present, else entirely exclude the field',
            'custom_url.required' => 'A custom url field maybe required',
            'custom_url.url'  => 'Custom url field characters are not valid, Url is expected',
            'custom_url.max'  => 'Custom url characters can not be more than 100',

            'description.sometimes' => 'A description field should be present, else entirely exclude the field',
            'description.required' => 'A description field maybe required',
            'description.string'  => 'Description field characters are not valid, String is expected',
            'description.max'  => 'Description characters can not be more than 16777215',

            'input_fields.required' => 'Input fields are required',
            'input_fields.array'  => 'Input fields characters are not valid, Array is expected',
            'input_fields.max'  => 'Input fields array can not contain more than 18 items',
            'input_fields.min'  => 'Input fields array can not have less than 1 item',

            'input_fields.first_name.required_unless' => 'First name field should be present, else entirely exclude the field',
            'input_fields.first_name.array'  => 'First name field is not valid, Array is expected',
            'input_fields.first_name.max'  => 'First name field can not be more than 2 items',
            'input_fields.first_name.min'  => 'First name field can not be less than 2 items',
            'input_fields.first_name.is_required.required_unless' => 'First name (is_required) field should be present, else entirely exclude the field',
            'input_fields.first_name.is_required.boolean'  => 'First name (is_required) field is not valid, Boolean is expected',
            'input_fields.first_name.is_shown.required_unless' => 'First name (is_shown) field should be present, else entirely exclude the field',
            'input_fields.first_name.is_shown.string'  => 'First name (is_shown) field is not valid, Boolean is expected',

            'input_fields.last_name.required_unless' => 'Last name field should be present, else entirely exclude the field',
            'input_fields.last_name.array'  => 'Last name field is not valid, Array is expected',
            'input_fields.last_name.max'  => 'Last name field can not be more than 2 items',
            'input_fields.last_name.min'  => 'Last name field can not be less than 2 items',
            'input_fields.last_name.is_required.required_unless' => 'Last name (is_required) field should be present, else entirely exclude the field',
            'input_fields.last_name.is_required.boolean'  => 'Last name (is_required) field is not valid, Boolean is expected',
            'input_fields.last_name.is_shown.required_unless' => 'Last name (is_shown) field should be present, else entirely exclude the field',
            'input_fields.last_name.is_shown.string'  => 'Last name (is_shown) field is not valid, Boolean is expected',

            'input_fields.whatsapp_number.required_unless' => 'Whatsapp number field should be present, else entirely exclude the field',
            'input_fields.whatsapp_number.array'  => 'Whatsapp number field is not valid, Array is expected',
            'input_fields.whatsapp_number.max'  => 'Whatsapp number field can not be more than 2 items',
            'input_fields.whatsapp_number.min'  => 'Whatsapp number field can not be less than 2 items',
            'input_fields.whatsapp_number.is_required.required_unless' => 'Whatsapp number (is_required) field should be present, else entirely exclude the field',
            'input_fields.whatsapp_number.is_required.boolean'  => 'Whatsapp number (is_required) field is not valid, Boolean is expected',
            'input_fields.whatsapp_number.is_shown.required_unless' => 'Whatsapp number (is_shown) field should be present, else entirely exclude the field',
            'input_fields.whatsapp_number.is_shown.string'  => 'Whatsapp number (is_shown) field is not valid, Boolean is expected',

            'input_fields.email_address.required_unless' => 'Email address field should be present, else entirely exclude the field',
            'input_fields.email_address.array'  => 'Email address field is not valid, Array is expected',
            'input_fields.email_address.max'  => 'Email address field can not be more than 2 items',
            'input_fields.email_address.min'  => 'Email address field can not be less than 2 items',
            'input_fields.email_address.is_required.required_unless' => 'Email address (is_required) field should be present, else entirely exclude the field',
            'input_fields.email_address.is_required.boolean'  => 'Email address (is_required) field is not valid, Boolean is expected',
            'input_fields.email_address.is_shown.required_unless' => 'Email address (is_shown) field should be present, else entirely exclude the field',
            'input_fields.email_address.is_shown.string'  => 'Email address (is_shown) field is not valid, Boolean is expected',

            'input_fields.house_address.required_unless' => 'House address field should be present, else entirely exclude the field',
            'input_fields.house_address.array'  => 'House address field is not valid, Array is expected',
            'input_fields.house_address.max'  => 'House address field can not be more than 2 items',
            'input_fields.house_address.min'  => 'House address field can not be less than 2 items',
            'input_fields.house_address.is_required.required_unless' => 'House address (is_required) field should be present, else entirely exclude the field',
            'input_fields.house_address.is_required.boolean'  => 'House address (is_required) field is not valid, Boolean is expected',
            'input_fields.house_address.is_shown.required_unless' => 'House address (is_shown) field should be present, else entirely exclude the field',
            'input_fields.house_address.is_shown.string'  => 'House address (is_shown) field is not valid, Boolean is expected',

            'input_fields.*.required_unless' => ':attribute field should be present, else entirely exclude the field',
            'input_fields.*.array'  => ':attribute field is not valid, Array is expected',
            'input_fields.*.max'  => ':attribute field can not be more than 2 items',
            'input_fields.*.min'  => ':attribute field can not be less than 2 items',
            'input_fields.*.is_required.required_unless' => ':attribute (is_required) field should be present, else entirely exclude the field',
            'input_fields.*.is_required.boolean'  => ':attribute (is_required) field is not valid, Boolean is expected',
            'input_fields.*.is_shown.required_unless' => ':attribute (is_shown) field should be present, else entirely exclude the field',
            'input_fields.*.is_shown.string'  => ':attribute (is_shown) field is not valid, Boolean is expected',

            'form_header_text.sometimes' => 'A form header text field should be present, else entirely exclude the field',
            'form_header_text.required' => 'A form header text field maybe required',
            'form_header_text.string'  => 'Form header text field characters are not valid, String is expected',
            'form_header_text.max'  => 'Form header text characters can not be more than 255',

            'form_header_photos.sometimes' => 'An array of photos should be present, else entirely exclude the field',
            'form_header_photos.required' => 'An array photos maybe required',
            'form_header_photos.array' => 'The photos field is not valid, Array is expected',
            'form_header_photos.max' => 'Photos array can contain only one(1) photo',
            'form_header_photos.filled' => 'Photos array can not be empty',
            'form_header_photos.*.image' => 'Must be an image',
            'form_header_photos.*.dimensions' => 'Image dimensions are a minimum width:200 and a minimum height:200',
            'form_header_photos.*.max' => 'Image size must be less than 2MB',
            'form_header_photos.*' => 'Only image files are allowed in photos field',
            'form_header_base64_photos.sometimes' => 'An array of base64 photos should be present, else entirely exclude the field',
            'form_header_base64_photos.required' => 'An array base64 photos maybe required',
            'form_header_base64_photos.array' => 'The base64 photos field is not valid, Array is expected',
            'form_header_base64_photos.max' => 'Base64 photos array can contain only one(1) photo',
            'form_header_base64_photos.filled' => 'Base64 photos array can not be empty',
            'form_header_url_photos.sometimes' => 'An array of url photos should be present, else entirely exclude the field',
            'form_header_url_photos.required' => 'An array of url photos maybe required',
            'form_header_url_photos.array' => 'The url photos field is not valid, Array is expected',
            'form_header_url_photos.max' => 'Url photos array can contain only one(1) photo',
            'form_header_url_photos.filled' => 'Url photos array can not be empty',

            'form_footer_text.sometimes' => 'A form footer text field should be present, else entirely exclude the field',
            'form_footer_text.required' => 'A form footer text field maybe required',
            'form_footer_text.string'  => 'Form footer text field characters are not valid, String is expected',
            'form_footer_text.max'  => 'Form footer text characters can not be more than 255',
            'form_footer_text.min'  => 'Form footer text characters can not be less than 1',

            'form_footer_photos.sometimes' => 'An array of photos should be present, else entirely exclude the field',
            'form_footer_photos.required' => 'An array photos maybe required',
            'form_footer_photos.array' => 'The photos field is not valid, Array is expected',
            'form_footer_photos.max' => 'Photos array can contain only one(1) photo',
            'form_footer_photos.filled' => 'Photos array can not be empty',
            'form_footer_photos.*.image' => 'Must be an image',
            'form_footer_photos.*.dimensions' => 'Image dimensions are a minimum width:200 and a minimum height:200',
            'form_footer_photos.*.max' => 'Image size must be less than 2MB',
            'form_footer_photos.*' => 'Only image files are allowed in photos field',
            'form_footer_base64_photos.sometimes' => 'An array of base64 photos should be present, else entirely exclude the field',
            'form_footer_base64_photos.required' => 'An array base64 photos maybe required',
            'form_footer_base64_photos.array' => 'The base64 photos field is not valid, Array is expected',
            'form_footer_base64_photos.max' => 'Base64 photos array can contain only one(1) photo',
            'form_footer_base64_photos.filled' => 'Base64 photos array can not be empty',
            'form_footer_url_photos.sometimes' => 'An array of url photos should be present, else entirely exclude the field',
            'form_footer_url_photos.required' => 'An array of url photos maybe required',
            'form_footer_url_photos.array' => 'The url photos field is not valid, Array is expected',
            'form_footer_url_photos.max' => 'Url photos array can contain only one(1) photo',
            'form_footer_url_photos.filled' => 'Url photos array can not be empty',

            'form_background_color.required' => 'A form background color field is required',
            'form_background_color.string'  => 'Form background color field characters are not valid, String is expected',
            'form_background_color.regex'  => 'Form background color field characters are not valid, Hex color code is expected',
            'form_background_color.max'  => 'Form background color characters can not be more than 20',

            'form_width.required' => 'A form width field is required',
            'form_width.in'  => 'Form width field characters are not valid, String of small, normal or large is expected',
            'form_width.max'  => 'Form width field characters can not be more than 30',

            'form_border_radius.sometimes' => 'A form border radius field should be present, else entirely exclude the field',
            'form_border_radius.required' => 'A form border radius field maybe required',
            'form_border_radius.integer'  => 'Form border radius field characters are not valid, Integer is expected',
            'form_border_radius.max'  => 'Form border radius field characters can not be more than 10',
            'form_border_radius.min'  => 'Form border radius field characters can not be less than 1',

            'submit_button_color.required' => 'A submit button color field is required',
            'submit_button_color.string'  => 'Submit button color field characters are not valid, String is expected',
            'submit_button_color.regex'  => 'Submit button color field characters are not valid, Hex color code is expected',
            'submit_button_color.max'  => 'Submit button color characters can not be more than 20',

            'submit_button_text.sometimes' => 'A submit button text field should be present, else entirely exclude the field',
            'submit_button_text.required' => 'A submit button text field maybe required',
            'submit_button_text.string'  => 'Submit button text field characters are not valid, String is expected',
            'submit_button_text.max'  => 'Submit button text characters can not be more than 100',
            'submit_button_text.min'  => 'Submit button text characters can not be less than 1',

            'submit_button_text_color.required' => 'A submit button text color field is required',
            'submit_button_text_color.string'  => 'Submit button text color field characters are not valid, String is expected',
            'submit_button_text_color.regex'  => 'Submit button text color field characters are not valid, Hex color code is expected',
            'submit_button_text_color.max'  => 'Submit button text color characters can not be more than 20',

            'submit_button_text_before.sometimes' => 'A submit button text before field should be present, else entirely exclude the field',
            'submit_button_text_before.required' => 'A submit button text before field maybe required',
            'submit_button_text_before.string'  => 'Submit button text before field characters are not valid, String is expected',
            'submit_button_text_before.max'  => 'Submit button text before characters can not be more than 100',
            'submit_button_text_before.min'  => 'Submit button text before characters can not be less than 1',

            'submit_button_text_after.sometimes' => 'A submit button text after field should be present, else entirely exclude the field',
            'submit_button_text_after.required' => 'A submit button text after field maybe required',
            'submit_button_text_after.string'  => 'Submit button text after field characters are not valid, String is expected',
            'submit_button_text_after.max'  => 'Submit button text after characters can not be more than 100',
            'submit_button_text_after.min'  => 'Submit button text after characters can not be less than 1',

            'thank_you_message.sometimes' => 'A thank you message field should be present, else entirely exclude the field',
            'thank_you_message.required' => 'A thank you message field maybe required',
            'thank_you_message.string'  => 'Thank you message field characters are not valid, String is expected',
            'thank_you_message.max'  => 'Thank you message characters can not be more than 255',
            'thank_you_message.min'  => 'Thank you message characters can not be less than 1',

            'thank_you_message_url.sometimes' => 'A thank you message url field should be present, else entirely exclude the field',
            'thank_you_message_url.required' => 'A thank you message url field maybe required',
            'thank_you_message_url.url'  => 'Thank you message url field characters are not valid, Url is expected',
            'thank_you_message_url.max'  => 'Thank you message url characters can not be more than 255',
            'thank_you_message.min'  => 'Thank you message url characters can not be less than 1',

            'facebook_pixel_code.sometimes' => 'A facebook pixel code field should be present, else entirely exclude the field',
            'facebook_pixel_code.required' => 'A facebook pixel code field maybe required',
            'facebook_pixel_code.string'  => 'Facebook pixel code field characters are not valid, String is expected',
            'facebook_pixel_code.max'  => 'Facebook pixel code characters can not be more than 100',
            'facebook_pixel_code.min'  => 'Facebook pixel code characters can not be less than 1',

            'auto_responder_id.sometimes' => 'An auto responder id field should be present, else entirely exclude the field',
            'auto_responder_id.required' => 'An auto responder id field maybe required',
            'auto_responder_id.string'  => 'Auto responder id field characters are not valid, Integer is expected',
            'auto_responder_id.max'  => 'Auto responder id characters can not be more than 255',
            'auto_responder_id.min'  => 'Auto responder id characters can not be less than 1',
        ];
    }
}
