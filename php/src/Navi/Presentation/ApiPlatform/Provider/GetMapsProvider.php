<?php

namespace App\Navi\Presentation\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Navi\Application\DTO\MapDTO;
use App\Navi\Application\Query\GetMaps\GetMapsQuery;
use App\Shared\Application\Query\QueryBusInterface;
use Exception;

final readonly class GetMapsProvider implements ProviderInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->mapRecordsToDTO($this->queryBus->execute(new GetMapsQuery()));
    }

    private function mapRecordsToDTO(array $records): array
    {
        return array_map(fn($record) => MapDTO::fromEntity($record), $records);
    }
}