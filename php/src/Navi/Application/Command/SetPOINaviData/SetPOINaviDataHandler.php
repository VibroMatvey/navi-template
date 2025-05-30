<?php

namespace App\Navi\Application\Command\SetPOINaviData;

use App\Navi\Domain\Repository\MapRepositoryInterface;
use App\Navi\Domain\Repository\POIRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Exception\ApiPlatform\DomainApiPlatformException;

final readonly class SetPOINaviDataHandler implements CommandHandlerInterface
{
    public function __construct(
        private POIRepositoryInterface $repository,
        private MapRepositoryInterface $mapRepository,
    )
    {
    }

    public function __invoke(
        SetPOINaviDataCommand $command
    ): string
    {
        $poi = $this->repository->find($command->poiUlid);
        if (!$poi) {
            throw new DomainApiPlatformException(
                'poi with ulid ' . $command->poiUlid . ' not found',
                404,
                101
            );
        }
        $poi->setNaviData($command->naviData);
        if ($command->mapUlid) {
            $map = $this->mapRepository->find($command->mapUlid);
            if (!$map) {
                throw new DomainApiPlatformException(
                    'map with ulid ' . $command->mapUlid . ' not found',
                    404,
                    102
                );
            }
            $poi->setMap($map);
        } else {
            $poi->setMap(null);
        }
        $this->repository->save($poi);
        return $poi->getUlid();
    }
}