<?php

namespace App\User\Application\Command\CreateUser;

use App\Shared\Application\Command\CommandInterface;

final readonly class CreateUserCommand implements CommandInterface
{
    public function __construct(
        public string  $username,
        public string  $password,
        public array   $roles,
    )
    {
    }
}