<?php

namespace App\Shared\Domain\Exception\ApiPlatform;

use RuntimeException;
use Throwable;

class DomainApiPlatformException extends RuntimeException
{
    protected int $statusCode;

    public function __construct(
        ?string    $message = "Внутренняя ошибка сервера",
        ?int       $statusCode = 500,
        ?int       $code = 0,
        ?Throwable $previous = null
    )
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}