<?php

namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\MapObject;
use App\Repository\MapObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class MapObjectDataProvider implements ProviderInterface
{
    public function __construct(
        private MapObjectRepository $mapObjectRepository,
    )
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return MapObject::class === $resourceClass;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->mapObjectRepository->findBy(['object' => null]);
    }
}
