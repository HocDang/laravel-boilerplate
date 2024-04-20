<?php

namespace App\Exceptions\Repository;

use Exception;
use Throwable;

class RepositoryException extends Exception
{
    /**
     * RepositoryException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Error", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
