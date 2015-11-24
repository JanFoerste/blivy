<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Request;

class Guard
{
    /**
     * ### Verifies the given CSRF-Token against the session token
     *
     * @return bool
     */
    public static function verifyCSRF()
    {
        if (getenv('CSRF_EVERYWHERE')) {
            $given = Input::get('csrf-token');
            $set = session_get('csrf_token');

            if ($given === $set) {
                return true;
            }
        } else {
            return true;
        }

        return false;
    }
}