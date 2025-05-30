<?php

namespace App\User\Domain\Entity;

use App\Shared\Domain\Service\UtilsService;
use App\Shared\Domain\Traits\TimestampTrait;
use App\Shared\Domain\Traits\UlidTrait;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UlidTrait, TimestampTrait;
    public const array ROLES = [
        'Администратор' => 'ROLE_ADMIN',
    ];

    private string $username;
    private string $password;
    private array $roles;
    private ?string $plainPassword = null;

    public function __construct()
    {
        $this->ulid = UtilsService::generateUlid();
    }

    public function getUlid(): ?string
    {
        return $this->ulid;
    }

    public function __toString(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Implement eraseCredentials() method if necessary.
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
}