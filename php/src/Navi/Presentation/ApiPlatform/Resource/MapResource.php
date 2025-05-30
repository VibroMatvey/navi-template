<?php

namespace App\Navi\Presentation\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use App\Navi\Application\DTO\MapDTO;
use App\Navi\Presentation\ApiPlatform\Provider\GetMapsProvider;

#[ApiResource(
    shortName: 'Navi',
    operations: [
        new GetCollection(
            uriTemplate: '/maps',
            openapi: new Operation(
                summary: 'Получение списка карт',
                description: '',
            ),
            paginationEnabled: false,
            paginationClientItemsPerPage: false,
            output: MapDTO::class,
            provider: GetMapsProvider::class,
        ),
    ],
)]
final readonly class MapResource
{
}