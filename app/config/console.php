<?php
/**
 * @author Jan Foerste <me@janfoerste.de>
 */

return [
    'commands' => [
        'help' => [
            'description' => 'Displays this help.',
            'class' => '\Blivy\Console\Help'
        ],
        'queue' => [
            'description' => 'Works on and manages queues.',
            'class' => '\Blivy\Console\Queue'
        ]
    ]
];