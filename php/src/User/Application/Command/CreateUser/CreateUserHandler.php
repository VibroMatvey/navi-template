<?php

namespace App\User\Application\Command\CreateUser;

use App\Shared\Application\Command\CommandHandlerInterface;
use App\User\Domain\Factory\UserFactory;
use App\User\Domain\Repository\UserRepositoryInterface;

final readonly class CreateUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }

    public function __invoke(CreateUserCommand $command): string
    {
        $user = UserFactory::create(
            $command->username,
            $command->password,
            $command->roles,
        );

        $this->userRepository->save($user);

        return $user->getUlid();
    }
}