<?php

namespace App\Navi\Application\Query\GetTerminals;

use App\Navi\Domain\Repository\TerminalRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

final readonly class GetTerminalsHandler implements QueryHandlerInterface
{
    public function __construct(
        private TerminalRepositoryInterface $repository
    )
    {
    }

    public function __invoke(
        GetTerminalsQuery $query
    ): array
    {
        return $this->repository->findTerminals($query->mapUlid);
    }
}