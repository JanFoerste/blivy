<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Http\Controllers;

use Blivy\Http\View\View;

class PageController
{
    public function home()
    {
        $view = new View('home');
        $view->set('word', 'template')
            ->set('test', 'Variable')
            ->set('extension', 'shared:extend');

        return $view->make();
    }
}