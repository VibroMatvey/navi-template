<?php

namespace App\User\Application\Query\GetUserByUsername;

use App\Shared\Application\Query\QueryHandlerInterface;
use App\User\Application\DTO\GetUserDTO;
use App\User\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

#[AsMessageHandler]
final readonly class GetUserByUsernameQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }

    public function __invoke(
        GetUserByUsernameQuery $query
    ): GetUserDTO
    {
        $user = $this->userRepository->findOneBy(['username' => $query->username]);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return GetUserDTO::fromEntity($user);
    }
}