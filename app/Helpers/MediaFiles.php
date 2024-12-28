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
class MediaFiles
{
    /**
     * @var array $merge_with
     */
    protected array $merge_with = [];

    /**
     * @var array $files
     */
    protected array $files = [];

    /**
     * @var array $base64_files
     */
    protected array $base64_files = [];

    /**
     * @var string $path
     */
    protected string $path;

    /**
     * @var int $limit
     */
    protected int $limit;

    /**
     * Create a new MediaFiles instance.
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
     * Set a limit for number of files to be stored
     * @param int $limit
     * @return MediaFiles
     */
    public function _limit(int $limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set a path for files to be stored
     * @param string $path
     * @return MediaFiles
     */
    public function _path(string $path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Sort uploaded file urls
     * @param null|array:string $file_urls
     * @return MediaImages
     */
    public function _fileUrls(?array $file_urls=[])
    {
        if (is_null($file_urls)){
            return $this;
        }

        foreach ($file_urls as $file_url) {
            if (filter_var($file_url, FILTER_VALIDATE_URL)) {
                $this->base64_files[] = 'data:@file/octet-stream;base64,'.base64_encode(file_get_contents($file_url));
            }
        }

        return $this;
    }

    /**
     * Sort uploaded base64 files
     * @param null|array:string $base64_files
     * @return MediaFiles
     */
    public function _base64Files(?array $base64_files=[])
    {
        if (is_null($base64_files)){
            return $this;
        }

        foreach ($base64_files as $base64_file) {
            if (is_string($base64_file)) {
                $this->base64_files[] = $base64_file;
            }
        }

        return $this;
    }

    /**
     * Sort uploaded files
     * @param null|array:Illuminate\Http\UploadedFile $files
     * @return MediaFiles
     */
    public function _files(?array $files=[])
    {
        if (is_null($files)){
            return $this;
        }

        foreach ($files as $file) {
            if (is_uploaded_file($file)) {
                $this->files[] = $file;
            }
        }

        return $this;
    }

    /**
     * Set a data to be returned with every response item
     * @param array $merge_with
     * @return MediaFiles
     */
    public function _merge(array $merge_with=[])
    {
        $this->merge_with = $merge_with;
        return $this;
    }

    /**
     * Store files
     * 
     * @param void
     * @return Illuminate\Support\Collection
     */
    public function _store()
    {
        // Check if there is a limit
        if (empty($this->limit)) {
            throw new Exception("MediaFiles: No limit was given", 1);
        }

        // Check if there is a path
        if (empty($this->path)) {
            throw new Exception("MediaFiles: No path was given", 1);
        }

        // Merge all uploaded file
        $files = array_merge($this->files, $this->base64_files);

        // Process files
        foreach ($files as $key => $file) {

            if ($key >= $this->limit){ break; }

            try {

                // Save file to storage
                if (is_uploaded_file($file)) {

                    $processed_media = $this->_storeFile($file, $this->path);

                } else if (is_string($file)) {

                    $processed_media = $this->_storeBase64File($file, $this->path);
                }

            } catch (\Throwable $th) {

                // Replace unsaved file to storage
                if ($generated_file = $this->_generateFile()){
                    $processed_media = $this->_storeFile($generated_file, $this->path);
                }
            }

            if ($processed_media) {

                // Results of the stored media
                $result = [
                    'id' => (string) Str::uuid(),
                    'file_name' => $processed_media->file_name,
                    'file_url'  => $processed_media->file_location,
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'updated_at' => now()->format('Y-m-d H:i:s'),
                ];

                // Merge additional key-value pairs to results of the stored media
                $stored_files[] = array_merge($result, $this->merge_with);
            }
        }

        return collect($stored_files ?? []);
    }

    /**
     * Un-store files
     * 
     * @param array:string $file_paths
     * @return Illuminate\Support\Collection
     */
    public function unStore(array $file_paths)
    {
        // Process files
        foreach ($file_paths as $file_path) {

            try {

                // Remove file from storage
                $processed_media = $this->_unStoreFile($file_path);

            } catch (\Throwable $th) {

                $processed_media = false;
            }

            if ($processed_media) {
                $unstored_files[] = $file_path;
            }
        }

        return collect($unstored_files ?? []);
    }

    /**
     * Replace files
	 * @param array:string $file_paths
     * @return Illuminate\Support\Collection
     */
    public function _replace(array $file_paths)
    {
        // Store files
        $stored_files = $this->_store();

        // Un-store files
        if ($stored_files->isNotEmpty()) {
            $this->_unStore($file_paths);
        }

        return $stored_files;
    }

    /**
     * Create a file object
     * 
     * @param void
     * @return object
     */
    public function _generateFile()
    {
        // Create an empty file
        $file = fopen("no_file.txt", "w");
        fclose($file);

        // Return generate file as an instance of Illuminate\Http\UploadedFile
        return new UploadedFile(new File('no_file.txt'), 'no_file.txt', 'text/plain', null, true);
    }

    /**
	 * Handle file upload
	 * Expects storage folder to have been linked for laravel frameworks
	 * Expects to use Illuminate\Support\Facades\Storage;
	 * @param Illuminate\Http\UploadedFile $image_file
     * @param string $store_folder
	 * @return object|false $saved_file|false
	 */
	public function _storeFile($file, $store_folder)
	{
		if ($file)
        {
        	// Allowed Parameters
        	$allowed_extension = array('doc','docx','pdf','txt','rtf');
        	$allowed_size = 2000000;

        	// Validate and store
            $file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $file_size = $file->getSize();
            $file_ext  = strtolower($file->getClientOriginalExtension());
            $file_name_to_store = mt_rand(0,999999).'_'.time().'.'.$file_ext;

            // Check if extension is allowed 
            if (!in_array($file_ext, $allowed_extension) || ($file_size > $allowed_size)) {
            	return false;
			}
			
			// Permanent file storage
			$path = $file->storeAs($store_folder, $file_name_to_store);

			// Create an object of the saved file properties
			if ($path) {
				$saved_file = (object)
				[
					'file_name' => $file_name_to_store,
					'file_location' => $path,
				];

				// Return a response
				if (Storage::exists($path)){
					return $saved_file;
				}
			}
		}

		return false;
    }

    /**
	 * Handle file removal
	 * Expects storage folder to have been linked for laravel frameworks
	 * Expects to use Illuminate\Support\Facades\Storage;
	 * @param  string $file_path
	 * @return boolean
	 */
	public function _unStoreFile($file_path)
	{
		if ($file_path) {

            // Check if file exists
            if (Storage::exists($file_path)) {

                // Delete file
                if (Storage::Delete($file_path)) {
                   return true;
                } else {

                    // Try unlink function if storage::delete failed
                    if (@unlink($file_path)) {
                        return true;
                    }
                }
            }
		}

		return false;
	}

    /**
	 * Handle file file upload
	 * Expects storage folder to have been linked for laravel frameworks
	 * Expects to use Illuminate\Support\Facades\Storage;
	 * @param string $file_string
     * @param string $store_folder
	 * @return object|false $saved_file|false
	 */
	public function _storeBase64File($file_string, $store_folder)
	{
		if ($file_string){

        	// Allowed Parameters
        	$allowed_extension = array('doc','docx','pdf','txt','rtf');
			$allowed_size = 2000000;

			// Check if string is a valid base64 file string
			if (preg_match('/^data:(\w+)\/(\S+);base64,/', $file_string)) {

				// Decode base64 file
				$file = base64_decode(substr($file_string, strpos($file_string, ',') + 1));

			} else {
				return false;
			}

			// Extract file extension
			$file_ext = (function($file_string){

				preg_match('/data:(.*?);base64/', $file_string, $match);
				$match = isset($match[1]) ? $match[1] : '';

				switch ($match) {
					case 'application/msword':
						return 'doc';

					case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
						return 'docx';

					case 'application/pdf':
						return 'pdf';

					case 'text/plain':
						return 'txt';

					case 'text/rtf':
						return 'rtf';

					default:
						return 'txt';
				}
			})($file_string);

			// Validate and store
            $file_size =  (int) (strlen(rtrim($file_string, '=')) * 3 / 4)/1024;
			$file_name_to_store = mt_rand(0,999999).'_'.time().'.'.$file_ext;
			$path = $store_folder.'/'.$file_name_to_store;

			// Check if extension is allowed
            if (!in_array($file_ext, $allowed_extension) || ($file_size > $allowed_size)) {
            	return false;
			}

			// Permanent file storage
			$storage = Storage::put($path, $file);

			// Create an object of the saved file properties
			if ($storage) {
				$saved_file = (object)
				[
					'file_name' => $file_name_to_store,
					'file_location' => $path,
				];

				// Return a response
				if (Storage::exists($path)){
					return $saved_file;
				}
			}
		}
	
		return false;
	}
}