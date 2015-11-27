<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Http\Controllers;

use Blivy\Http\View\View;
use Blivy\Queue\Drivers\Connector;
use Blivy\Queue\Job\TestJob;
use Blivy\Queue\QueueWorker;

class PageController
{
    public function home()
    {
        //new TestJob('Job Queue!');

        $connection = Connector::get();
        pr($connection->getFailedJobs());

        $view = new View('home');
        $view->set('extension', 'shared:extend');

        return $view->make();
    }
}