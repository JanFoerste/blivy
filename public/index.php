<?php
/**
 * ### Welcome to the Blivy PHP-Framework
 *
 * ### https://github.com/JanFoerste/blivy
 *
 * @author Jan Foerste <me@janfoerste.de>
 */

/**
 * ### Loads the composer autoloader
 */
require __DIR__ . '/../vendor/autoload.php';

/**
 * ### Load other requirements
 */
require __DIR__ . '/../bootstrap/app.php';

/**
 * ### Start the request
 */

new \Manager\Request\Request();
