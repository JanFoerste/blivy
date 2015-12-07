<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

namespace Blivy\Queue\Job;


interface JobInterface
{
    public function handle();
}