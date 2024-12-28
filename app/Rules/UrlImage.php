<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * The file under validation must be an image url (jpeg, png, bmp, gif, svg, or webp)
 * 
 * $request->validate([
 *     'field' => [new UrlImage([
 *         'max_width'=>'1000',
 *         'max_height'=>'2000',
 *         'min_width'=>'100',
 *         'min_height'=>'200',
 *          width'=>'1000',
 *         'height'=>'2000',
 *         'ratio'=>'2/1',
 *         'mimes'=>['jpg','png','gif'],
 *         'mimetypes'=>['video/avi','video/mpeg','video/quicktime'],
 *         'min_size'=>'13',
 *         'max_size'=>'15'
 *     ])]
 * ]);
 */
class UrlImage implements Rule
{
    /**
     * Holds the constraints
     *
     * @var array
    */
    protected array $constraints = [];

    /**
     *  Holds the base64 image failed validations
     *
     * @var array
    */
    protected $message_bag = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * Get all values in the message bag
     * 
     * @param string|void $var
     * @param array
     */
    public function getFromMessageBag($var = null)
    {
        return $var? $this->message_bag[$var] : $this->message_bag;
    }

    /**
     * Append a value to the message bag
     * 
     * @param array $var
     * @return array
     */
    public function appendToMessageBag(array $var)
    {
        return $this->message_bag = array_merge($this->message_bag,$var);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Download given url as base64 images
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->appendToMessageBag([':input is not a valid url for an image file.']);
            return false;
        }

        // Check if string is a valid base64 image string
        try {

            $base64_image = 'data:image/png;base64,'.base64_encode(file_get_contents($value));
            $image_info = getimagesize($base64_image);
            $image_width = (int) $image_info[0];
            $image_height = (int) $image_info[1];
            $image_ratio = $image_width / $image_height;
            $image_bits = $image_info[4] ?? null;
            $image_mine = $image_info[5] ?? mime_content_type($base64_image);
            $image_ext = explode('/',$image_mine)[1];
            $image_size = (int) (strlen(rtrim($base64_image, '=')) * 3 / 4)/1024;

        } catch (\Throwable $th) {

            $this->appendToMessageBag([':attribute is not a valid image file.']);
            return false;
        }

        // Ratio constrain
        if (isset($this->constraints['ratio']) && ($image_ratio !== eval('return '.$this->constraints['ratio'].';')) ) {
            $this->appendToMessageBag([':attribute required ratio is '.$this->constraints['ratio'].' (width/height).']);
        }

        // Width constrain
        if (isset($this->constraints['width']) && ((int) $this->constraints['width'] !== $image_width )) {
            $this->appendToMessageBag([':attribute width is not equal to '.$this->constraints['width'].'px.']);
        }

        // Height constrain
        if (isset($this->constraints['height']) && ((int) $this->constraints['height'] !== $image_height )) {
            $this->appendToMessageBag([':attribute height is not equal to '.$this->constraints['height'].'px.']);
        }

        // Min width constrain
        if (isset($this->constraints['min_width']) && ($image_width < (int) $this->constraints['min_width'] )) {
            $this->appendToMessageBag([':attribute minimum required width is '.$this->constraints['min_width'].'px.']);
        }

        // Min height constrain
        if (isset($this->constraints['min_height']) && ($image_height < (int) $this->constraints['min_height'] )) {
            $this->appendToMessageBag([':attribute minimum required height is '.$this->constraints['min_height'].'px.']);
        }

        // Max width constrain
        if (isset($this->constraints['max_width']) && ($image_width > (int) $this->constraints['max_width'] )) {
            $this->appendToMessageBag([':attribute maximum required width is '.$this->constraints['max_width'].'px.']);
        }

        // Max height constrain
        if (isset($this->constraints['max_height']) && ($image_height > (int) $this->constraints['max_height'] )) {
            $this->appendToMessageBag([':attribute maximum required height is '.$this->constraints['max_height'].'px.']);
        }

        // Mimes constrain
        if (isset($this->constraints['mimes']) && !in_array($image_ext, $this->constraints['mimes'], true) ) {
            $this->appendToMessageBag([':attribute must match one of the given MIMES : '.implode(", ",$this->constraints['mimes'])]);
        }

        // Mimes type constrain
        if (isset($this->constraints['mimetypes']) && !in_array($image_mine, $this->constraints['mimetypes'], true) ) {
            $this->appendToMessageBag([':attribute must match one of the given MIME types: '.implode(", ",$this->constraints['mimetypes'])]);
        }

        // Min size constrain
        if (isset($this->constraints['min_size']) && ( $image_size < (int) $this->constraints['min_size'] )) {
            $this->appendToMessageBag([':attribute file size can not be less than '.$this->constraints['min_size'].'kb.']);
        }

        // Max size constrain
        if (isset($this->constraints['max_size']) && ($image_size > (int) $this->constraints['max_size'] )) {
            $this->appendToMessageBag([':attribute file size can not be more than '.$this->constraints['max_size'].'kb.']);
        }

        // Check if message bag is empty
        return empty($this->getFromMessageBag())? true : false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->getFromMessageBag();
    }
}
