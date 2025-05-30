<?php

namespace App\Navi\Presentation\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Navi\Application\DTO\TerminalDTO;
use App\Navi\Application\Query\GetTerminals\GetTerminalsQuery;
use App\Shared\Application\Query\QueryBusInterface;
use Exception;

final readonly class GetTerminalsProvider implements ProviderInterface
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
        return $this->mapRecordsToDTO($this->queryBus->execute(new GetTerminalsQuery(
            $query->get('mapUlid'),
        )));
    }

    private function mapRecordsToDTO(array $records): array
    {
        return array_map(fn($record) => TerminalDTO::fromEntity($record), $records);
    }
}