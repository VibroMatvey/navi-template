<?php

namespace App\Navi\Application\Query\GetPOIs;

use App\Navi\Domain\Repository\POIRepositoryInterface;
use App\Shared\Application\Query\QueryHandlerInterface;

final readonly class GetPOIsHandler implements QueryHandlerInterface
{
    public function __construct(
        private POIRepositoryInterface $repository
    )
    {
    }

    public function __invoke(
        GetPOIsQuery $query
    ): array
    {
        return $this->repository->findPOIs($query->mapUlid);
    }
}