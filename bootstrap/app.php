<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

/**
 * ### Basic helper functions globally available
 */
require_once __DIR__ . '/../app/helpers.php';

/**
 * ### Helpers to be used in the view classes and templates
 */
require_once __DIR__ . '/../app/Http/View/helpers.php';

/**
 * ### Set up the ORM if config exists
 */
if (file_exists(__DIR__ . '/../generated-conf/config.php')) require_once __DIR__ . '/../generated-conf/config.php';

/**
 * ### Sets up the dotenv system
 */
$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();
