<?php

namespace App\Navi\Application\Command\DeletePOI;

use App\Navi\Domain\Repository\POIRepositoryInterface;
use App\Navi\Domain\Service\NaviServiceInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use RuntimeException;

final readonly class DeletePOIHandler implements CommandHandlerInterface
{
    public function __construct(
        private POIRepositoryInterface $repository,
        private NaviServiceInterface   $naviService,
    )
    {
    }

    public function __invoke(
        DeletePOICommand $command
    ): void
    {
        $poi = $this->repository->find($command->poUlid);
        if (!$poi) {
            throw new RuntimeException(
                'poi not found',
            );
        }
        if ($poi->getNaviData()) {
            if ($poi->getNaviData()['type'] == 'point') {
                $this->naviService->deletePoint($poi->getNaviData()['pointId']);
            } else {
                $this->naviService->deleteArea($poi->getNaviData()['areaId']);
            }
        }
        $this->repository->remove($poi);
    }
}