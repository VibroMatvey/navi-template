<?php

namespace App\User\Application\Command\EditUser;

use App\Shared\Application\Command\CommandHandlerInterface;
use App\User\Domain\Entity\User;
use App\User\Domain\Factory\UserFactory;
use App\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final readonly class EditUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }

    public function __invoke(
        EditUserCommand $command
    ): string
    {
        /* @var User $user */
        $user = $this->userRepository->find($command->ulid);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user = UserFactory::edit(
            $user,
            $command->username,
            $command->password,
            $command->roles,
        );

        $this->userRepository->save($user);

        return $user->getUlid();
    }
}