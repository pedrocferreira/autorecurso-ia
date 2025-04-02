<?php

namespace App\Exceptions;

use Exception;

class InsufficientCreditsException extends Exception
{
    /**
     * Create a new insufficient credits exception instance.
     *
     * @param  string  $message
     * @return void
     */
    public function __construct($message = 'Créditos insuficientes para realizar esta operação.')
    {
        parent::__construct($message);
    }
} 