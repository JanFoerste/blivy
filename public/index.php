<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

new \Manager\Request\Request();
$GLOBALS['time'] = microtime(true);