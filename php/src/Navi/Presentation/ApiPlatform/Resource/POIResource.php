<?php

namespace App\Navi\Presentation\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Navi\Application\DTO\POIDTO;
use App\Navi\Presentation\ApiPlatform\Provider\GetPOIsProvider;

#[ApiResource(
    shortName: 'Navi',
    operations: [
        new GetCollection(
            uriTemplate: '/points-of-interest',
            openapi: new Operation(
                summary: 'Получение списка точек интереса',
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
            output: POIDto::class,
            provider: GetPOIsProvider::class,
        ),
    ],
)]
final readonly class POIResource
{
}