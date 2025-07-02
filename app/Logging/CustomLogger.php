<?php

namespace App\Logging;

use Monolog\Logger;

class CustomLogger extends Logger
{
    public function __construct($name = 'custom')
    {
        parent::__construct($name);
    }
} 