<?php

namespace App\User\Application\Command\EditUser;

use App\Shared\Application\Command\CommandInterface;

final readonly class EditUserCommand implements CommandInterface
{
    public function __construct(
        public string  $ulid,
        public ?string $username,
        public ?string $password,
        public ?array  $roles,
    )
    {
    }
}