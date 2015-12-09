<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Http\Middleware;


use Blivy\Exception\NotAuthorizedException;

class Auth
{
    /**
     * Auth middleware constructor.
     */
    public function __construct()
    {
        if (\Blivy\Auth\Auth::check()) {
            return true;
        } else {
            throw new NotAuthorizedException;
        }
    }
}