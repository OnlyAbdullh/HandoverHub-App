<?php

namespace App\Exceptions;

use Exception;

class EngineException extends Exception
{
    public function __construct($message = 'Engine operation failed', $code = 400)
    {
        parent::__construct($message, $code);
    }
}
