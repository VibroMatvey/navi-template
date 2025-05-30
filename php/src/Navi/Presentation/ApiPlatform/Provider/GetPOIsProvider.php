<?php

namespace App\Navi\Presentation\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Navi\Application\DTO\POIDTO;
use App\Navi\Application\Query\GetPOIs\GetPOIsQuery;
use App\Shared\Application\Query\QueryBusInterface;
use Exception;

final readonly class GetPOIsProvider implements ProviderInterface
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
        $query = $context['request']->query;
        return $this->mapRecordsToDTO($this->queryBus->execute(new GetPOIsQuery(
            $query->get('mapUlid'),
        )));
    }

    private function mapRecordsToDTO(array $records): array
    {
        return array_map(fn($record) => POIDTO::fromEntity($record), $records);
    }
}