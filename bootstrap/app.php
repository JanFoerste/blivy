<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

/**
 * ### Files and classes required before anything else
 */

require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/Http/View/helpers.php';
require_once __DIR__ . '/../app/Exception/Exception.php';
if (file_exists(__DIR__ . '/../generated-conf/config.php')) require_once __DIR__ . '/../generated-conf/config.php';

/**
 * ### Import everything in these folders (excluding subfolders)
 */

$folders = [
    'Exception',
    'Auth',
];

foreach ($folders as $folder) {
    foreach (glob(__DIR__ . '/../app/' . $folder . '/*.php') as $filename) {
        require_once $filename;
    }
}

/**
 * ### Sets up the dotenv system
 */
$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();
