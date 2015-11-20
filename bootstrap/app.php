<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/Http/View/helpers.php';
require_once __DIR__ . '/../app/Exception/Exception.php';
require_once __DIR__ . '/../generated-conf/config.php';

$folders = [
    'Exception',
    'Auth',
];

foreach ($folders as $folder) {
    foreach (glob(__DIR__ . '/../app/' . $folder . '/*.php') as $filename) {
        require_once $filename;
    }
}

$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();
