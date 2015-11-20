<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Manager\Http\Controllers;

use Manager\Http\View\View;

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