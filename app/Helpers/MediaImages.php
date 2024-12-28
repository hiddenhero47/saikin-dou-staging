<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * Use laravel storage function 
 */
class MediaImages
{
    /**
     * @var array $merge_with
     */
    protected array $merge_with = [];

    /**
     * @var array $images
     */
    protected array $images = [];

    /**
     * @var array $base64_images
     */
    protected array $base64_images = [];

    /**
     * @var string $path
     */
    protected string $path;

    /**
     * @var int $limit
     */
    protected int $limit;

    /**
     * Create a new MediaImages instance.
     * @param void
     * @return void
     */
    public function __construct() 
    {
        return $this;
    }

    /**
     * This would pass calls to $this->fn() to $this->fn() when method exists
     * This would pass calls to $this->fn() to $this->_fn() when method does not exist
     */
    public function __call($method, $args) {
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $args);
        }

        if (method_exists($this, '_'.$method)) {
            return call_user_func_array([$this, '_'.$method], $args);
        }
    }

    /**
     * This would pass calls to self::fn() to self::fn() when method exists
     * This would pass calls to self::fn() to $this->_fn() when method does not exist
     */
    public static function __callStatic($method, $args) {
        if (method_exists(__CLASS__, $method)) {
            return call_user_func_array([__CLASS__, $method], $args);
        }

        $instance = new self();
        if (method_exists($instance, '_'.$method)) {
            return call_user_func_array([$instance, '_'.$method ], $args);
        }
    }

    /**
     * Set a limit for number of image files to be stored
     * @param int $limit
     * @return MediaImages
     */
    public function _limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set a path for image files to be stored
     * @param string $path
     * @return MediaImages
     */
    public function _path(string $path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Sort uploaded image urls
     * @param null|array:string $image_urls
     * @return MediaImages
     */
    public function _imageUrls(?array $image_urls=[])
    {
        if (is_null($image_urls)){
            return $this;
        }

        foreach ($image_urls as $image_url) {
            if (filter_var($image_url, FILTER_VALIDATE_URL)) {
                $this->base64_images[] = 'data:image/png;base64,'.base64_encode(file_get_contents($image_url));
            }
        }

        return $this;
    }

    /**
     * Sort uploaded base64 image files
     * @param null|array:string $base64_images
     * @return MediaImages
     */
    public function _base64Images(?array $base64_images=[])
    {
        if (is_null($base64_images)){
            return $this;
        }

        foreach ($base64_images as $base64_image) {
            if (is_string($base64_image)) {
                $this->base64_images[] = $base64_image;
            }
        }

        return $this;
    }

    /**
     * Sort uploaded image files
     * @param null|array:Illuminate\Http\UploadedFile $images
     * @return MediaImages
     */
    public function _images(?array $images=[])
    {
        if (is_null($images)){
            return $this;
        }

        foreach ($images as $image) {
            if (is_uploaded_file($image)) {
                $this->images[] = $image;
            }
        }

        return $this;
    }

    /**
     * Set a data to be returned with every response item
     * @param array $merge_with
     * @return MediaImages
     */
    public function _merge(array $merge_with=[])
    {
        $this->merge_with = $merge_with;
        return $this;
    }

    /**
     * Store images
     * @param void
     * @return Illuminate\Support\Collection
     */
    public function _store()
    {
        // Check if there is a limit
        if (empty($this->limit)) {
            throw new Exception("MediaImages: No limit was given", 1);
        }

        // Check if there is a path
        if (empty($this->path)) {
            throw new Exception("MediaImages: No path was given", 1);
        }

        // Merge all uploaded image file
        $image_files = array_merge($this->images, $this->base64_images);

        // Process images
        foreach ($image_files as $key => $image_file) {

            if ($key >= $this->limit){ break; }

            try {

                // Save image to storage
                if (is_uploaded_file($image_file)) {

                    $processed_media = $this->_storeImage($image_file, $this->path);

                } else if (is_string($image_file)) {

                    $processed_media = $this->_storeBase64Image($image_file, $this->path);
                }

            } catch (\Throwable $th) {

                // Replace unsaved image to storage
                if ($generated_image = $this->_generateImage()){
                    $processed_media = $this->_storeImage($generated_image, $this->path);
                }
            }

            if ($processed_media) {

                // Results of the stored media
                $result = [
                    'id' => (string) Str::uuid(),
                    'image_name' => $processed_media->file_name,
                    'image_url'  => $processed_media->file_location,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];

                // Merge additional key-value pairs to results of the stored media
                $stored_images[] = array_merge($result, $this->merge_with);
            }
        }

        return collect($stored_images ?? []);
    }

    /**
     * Un-store images
	 * @param array:string $image_paths
     * @return Illuminate\Support\Collection
     */
    public function _unStore(array $image_paths)
    {
        // Process images
        foreach ($image_paths as $image_path) {

            try {

                // Remove image from storage
                $processed_media = $this->_unStoreImage($image_path);

            } catch (\Throwable $th) {

                $processed_media = false;
            }

            if ($processed_media) {
                $unstored_images[] = $image_path;
            }
        }

        return collect($unstored_images ?? []);
    }

    /**
     * Replace images
	 * @param array:string $image_paths
     * @return Illuminate\Support\Collection
     */
    public function _replace(array $image_paths)
    {
        // Store images
        $stored_images = $this->_store();

        // Un-store images
        if ($stored_images->isNotEmpty()) {
            $this->_unStore($image_paths);
        }

        return $stored_images;
    }

    /**
     * Create an image object
     * @param void
     * @return object
     */
    public function _generateImage()
    {
        // Generate image
        $im = @imagecreate(250, 250);
        $background_color = imagecolorallocate($im, 255, 255, 255);
        $text_color = imagecolorallocate($im, 128, 128, 128);
        imagestring($im, 5, 93, 111,  "No Image", $text_color);

        // Create an empty file
        $file = fopen("no_image.png", "w");
        fclose($file);

        // Save image to generated file
        imagepng($im, 'no_image.png', 0);

        // Return generate file as an instance of Illuminate\Http\UploadedFile
        return new UploadedFile(new File('no_image.png'), 'no_image.png', 'image/png', null, true);
    }

    /**
	 * Handle image file upload
	 * Expects storage folder to have been linked for laravel frameworks
	 * Expects to use Illuminate\Support\Facades\Storage;
	 * @param  Illuminate\Http\UploadedFile $image_file
     * @param string $store_folder
	 * @return object|false $saved_image|false
	 */
	public function _storeImage($image_file, $store_folder)
	{
		if ($image_file){

        	// Allowed Parameters
        	$allowed_extension = array('jpg','jpeg','png','gif','bmp');
        	$allowed_size = 2000000;

        	// Validate and store
            $file_name = pathinfo($image_file->getClientOriginalName(), PATHINFO_FILENAME);
            $file_size = $image_file->getSize();
            $file_ext  = strtolower($image_file->getClientOriginalExtension());
			$file_name_to_store = mt_rand(0,999999).'_'.time().'.'.$file_ext;

            // Check if extension is allowed 
            if (!in_array($file_ext, $allowed_extension) || ($file_size > $allowed_size)) {
            	return false;
			}

			// Permanent file storage
			$path = $image_file->storeAs($store_folder, $file_name_to_store);

			// Create an object of the saved image properties
			if ($path) {
				$saved_image = (object)
				[
					'file_name' => $file_name_to_store,
					'file_location' => $path,
				];

				// Return a response
				if (Storage::exists($path)){
					return $saved_image;
				}
			}
		}

		return false;
    }

    /**
	 * Handle image file removal
	 * Expects storage folder to have been linked for laravel frameworks
	 * Expects to use Illuminate\Support\Facades\Storage;
	 * @param string $image_path
	 * @return boolean
	 */
	public function _unStoreImage($image_path)
	{
		if ($image_path) {

            // Check if file exists
            if (Storage::exists($image_path)) {

                // Delete file
                if (Storage::Delete($image_path)) {
                   return true;
                } else {

                    // Try unlink function if storage::delete failed
                    if (@unlink($image_path)) {
                        return true;
                    }
                }
            }
		}

		return false;
	}

    /**
	 * Handle image file upload
	 * Expects storage folder to have been linked for laravel frameworks
	 * Expects to use Illuminate\Support\Facades\Storage;
	 * @param string $image_string
     * @param string $store_folder
	 * @return object|false $saved_image|false
	 */
	public function _storeBase64Image($image_string, $store_folder)
	{
		if ($image_string){

        	// Allowed Parameters
        	$allowed_extension = array('jpg','jpeg','png','gif','bmp');
			$allowed_size = 2000000;

			// Check if string is a valid base64 image string
			if (preg_match('/^data:image\/(\w+);base64,/', $image_string)) {

				// Decode base64 image
				$image = base64_decode(substr($image_string, strpos($image_string, ',') + 1));

			} else {
				return false;
			}

        	// Validate and store
            $file_size =  (int) (strlen(rtrim($image_string, '=')) * 3 / 4)/1024;
			$file_ext = explode('/', mime_content_type($image_string))[1];
			$file_name_to_store = mt_rand(0,999999).'_'.time().'.'.$file_ext;
			$path = $store_folder.'/'.$file_name_to_store;

			// Check if extension is allowed
            if (!in_array($file_ext, $allowed_extension) || ($file_size > $allowed_size)) {
            	return false;
			}

			// Permanent file storage
			$storage = Storage::put($path, $image);

			// Create an object of the saved image properties
			if ($storage) {
				$saved_image = (object)
				[
					'file_name' => $file_name_to_store,
					'file_location' => $path,
				];

				// Return a response
				if (Storage::exists($path)){
					return $saved_image;
				}
			}
		}

		return false;
    }
}