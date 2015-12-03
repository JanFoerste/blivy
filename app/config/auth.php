<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

return [
    'driver' => 'db',

    'session_lifetime' => 60,

    'providers' => [

        'db' => [
            'class' => '\Blivy\Auth\DBAuthProvider',

            'auth_model' => '\Models\UserQuery'
        ]
    ]
];