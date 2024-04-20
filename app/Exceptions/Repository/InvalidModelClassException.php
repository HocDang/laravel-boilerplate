<?php

namespace App\Exceptions\Repository;

use Exception;
use Throwable;

class InvalidModelClassException extends Exception
{
    /**
     * InvalidModelClassException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Invalid model class", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
