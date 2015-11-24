<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

return [
    'protocol' => env('MAIL_PROTOCOL', 'smtp'),
    'host' => env('MAIL_HOST', '127.0.0.1'),
    'port' => env('MAIL_PORT', 587),
    'from' => ['dev@janfoerste.de', 'Dev'],
    'username' => env('MAIL_USERNAME', ''),
    'password' => env('MAIL_PASSWORD', ''),
    'encryption' => env('MAIL_ENCRYPTION', 'tls')
];