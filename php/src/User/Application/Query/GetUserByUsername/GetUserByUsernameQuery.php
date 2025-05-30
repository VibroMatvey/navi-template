<?php

namespace App\User\Application\Query\GetUserByUsername;

use App\Shared\Application\Query\QueryInterface;

final readonly class GetUserByUsernameQuery implements QueryInterface
{
    public function __construct(
        public string $username
    )
    {
    }
}