<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Auth;

/**
 * ### En- and decryption as well as hashing is done here
 *
 * Class Crypt
 * @package Manager\Auth
 */
class Crypt
{
    /**
     * ### Hashes the given value. Used for storing passwords
     *
     * @param $value
     * @return bool|string
     */
    public static function hash($value)
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    /**
     * ### Encrypts the given value with the application key
     *
     * @param $value
     * @return string
     */
    public static function encrypt($value)
    {
        $key = getenv('APP_KEY');
        $iv = openssl_random_pseudo_bytes(16);
        $enc = openssl_encrypt($value, 'aes128', $key, 0, $iv);
        $output = base64_encode($iv . '|' . $enc);
        return $output;
    }

    /**
     * ### Decrypts the given value with the application key
     *
     * @param $value
     * @return string
     */
    public static function decrypt($value)
    {
        $key = getenv('APP_KEY');
        $value = base64_decode($value);
        $explode = explode('|', $value);

        $dec = openssl_decrypt($explode[1], 'aes128', $key, 0, $explode[0]);
        return $dec;
    }
}