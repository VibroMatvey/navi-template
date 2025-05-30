<?php

namespace App\Navi\Application\Query\GetMaps;

use App\Navi\Domain\Repository\MapRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

final readonly class GetMapsHandler implements QueryHandlerInterface
{
    public function __construct(
        private MapRepositoryInterface $repository
    )
    {
    }

    public function __invoke(
        GetMapsQuery $query
    ): array
    {
        return $this->repository->findAll();
    }
}