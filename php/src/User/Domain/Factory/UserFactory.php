<?php

namespace App\User\Domain\Factory;

use App\Shared\Domain\Service\UtilsService;
use App\User\Domain\Entity\User;

class UserFactory
{
    public static function create(
        string  $username,
        string  $password,
        array   $roles,
    ): User
    {
        return (new User())
            ->setUsername($username)
            ->setRoles($roles)
            ->setPassword(UtilsService::generatePassword($password));
    }

    public static function edit(
        User    $user,
        ?string $username,
        ?string $password,
        ?array  $roles,
    ): User
    {
        if ($username) {
            $user->setUsername($username);
        }
        if ($roles) {
            $user->setRoles($roles);
        }
        if ($password != null && $password != $user->getPassword()) {
            $user->setPassword(UtilsService::generatePassword($password));
        }

        return $user;
    }
}