<?php

namespace App\User\Application\DTO;

use App\User\Domain\Entity\User;

class GetUserDTO
{
    public function __construct(
        public string $ulid,
        public string $username,
        public array  $roles,
    )
    {
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->getUlid(),
            $user->getUsername(),
            $user->getRoles()
        );
    }
}