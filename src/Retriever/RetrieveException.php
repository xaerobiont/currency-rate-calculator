<?php

declare(strict_types=1);

namespace App\Retriever;

use Symfony\Component\HttpKernel\Exception\HttpException;

class RetrieveException extends HttpException
{
    public function __construct(
        int $statusCode = 500,
        string $message = 'Could not retrieve currency rates',
        \Throwable $previous = null,
        array $headers = [],
        int $code = 0
    )
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}