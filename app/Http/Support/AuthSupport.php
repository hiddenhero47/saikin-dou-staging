<?php

namespace App\Http\Support;

use App\Models\User;

class AuthSupport
{
    protected static $error_bag = [];

    /**
     * Generate a unique name
     * 
     * @param string|null $name
     * @return string $name
     */
    public static function uniqueName(?string $name)
    {
        $name = strtolower(str_replace(' ','.',trim($name ?? 'user')));
        $name = strlen($name) > 50 ? 'user' : $name;
        $exists = User::where('name',$name)->exists();

        return $exists ? self::uniqueName($name.rand(1,9)) : $name;
    }

    /**
     * Creates an email verification token.
     * 
     * @param string $email
     * @return string|null $token|null
     */
    public static function createVerificationToken(string $email)
    {
        // Create a token generator
        $token_maker = function ($token_key, $email){

            // Make a new token
            try {
                // Split both strings to arrays of grouped types and merge
                $array = array_merge(
                    str_split(strtok($email,'@'), 1), 
                    str_split($token_key, 1)
                );

                // Sort array values in order of natural numbers
                natsort($array);

                // Take all the array keys only
                $array = array_keys($array);
                $token = '';

                // Mesh array key and values in to a string
                foreach ($array as $key => $value) {
                    $token .= $key.$value; 
                }

                // Hash the string
                $token = md5($token);

                // Return results
                return $token;

            } catch (\Throwable $th) {
                return false;
            }
        };

        // Create a token
        $token = $token_maker(config('app.key'), $email);

        // Return success
        if (strlen($token) === 32 && ctype_xdigit($token)) {
            return $token;
        }

        // Return failure
        return null;
    }

    /**
     * Creates an email verification link.
     * 
     * @param string $email
     * @return string $url
     */
    public static function createVerificationLink(string $email)
    {
        return config('app.url').'/api/auth/verify?email='.$email.'&token='.self::createVerificationToken($email);
    }

    /**
     * Validates email verification token.
     * 
     * @param string $email
     * @param string $token
     * @return boolean
     */
    public static function checkVerificationToken(string $email, string $token)
    {
        // Recreate and compare token
        return self::createVerificationToken($email) === $token;
    }

    /**
     * Checks if user email is verified.
     * 
     * @param App\Models\User $user
     * @return boolean
     */
    public static function checkEmailVerification(User $user)
    {
        /**
         * Check if email verification is enabled
         */
        if (!config('ov.email_verification')) {
            return true;
        }

        /**
         * Check if user email is verified.
         */
        if ($user->email_verified_at) {
            return true;
        }

        /**
         * Gets time in which user account was created 
         * Adds stated grace period to time user account was created
         * Checks if user is still within grace period
         * Lets user access the app.
         */
        $grace_period = strtotime($user->created_at) + (config('ov.grace_period')*60);
        if ($grace_period > time()) {
            return true;
        }

        // Return failure
        return false;
    }
}