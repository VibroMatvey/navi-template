<?php

namespace App\Navi\Presentation\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Navi\Application\DTO\TerminalDTO;
use App\Navi\Presentation\ApiPlatform\Provider\GetTerminalsProvider;

#[ApiResource(
    shortName: 'Navi',
    operations: [
        new GetCollection(
            uriTemplate: '/terminals',
            openapi: new Operation(
                summary: 'Получение списка терминалов',
                description: '',
                parameters: [
                    new Parameter(
                        'mapUlid',
                        'query',
                        'map ulid',
                        false
                    )
                ]
            ),
            paginationEnabled: false,
            paginationClientItemsPerPage: false,
            output: TerminalDTO::class,
            provider: GetTerminalsProvider::class,
        ),
    ],
)]
final readonly class TerminalResource
{
}