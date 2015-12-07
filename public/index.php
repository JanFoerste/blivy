<?php
/**
 * ### Welcome to the Blivy framework
 *
 * ### https://github.com/JanFoerste/blivy
 *
 * @author Jan Foerste <me@janfoerste.de>
 */

/**
 * ### Autoload files
 */
require __DIR__ . '/../bootstrap/autoload.php';

/**
 * ### Load other requirements
 */
require_once __DIR__ . '/../bootstrap/app.php';

/**
 * ### Start the request
 */

new \Blivy\Request\Request();
