<?php

namespace App\Navi\Presentation\ApiPlatform\Controller\SetPOINaviData;

use App\Navi\Application\Command\SetPOINaviData\SetPOINaviDataCommand;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController, Route(path: '/api/points-of-interest/{ulid}/navi', name: 'set_poi_navi_data', methods: ['POST'])]
final class SetPOINaviDataController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    )
    {
    }

    public function __invoke(
        $ulid,
        #[MapRequestPayload] SetPOINaviDataInput $input
    ): JsonResponse
    {
        $naviData = null;
        if ($input->area) {
            $naviData = (array)$input->area;
        }
        if ($input->point) {
            $naviData = (array)$input->point;
        }
        $poiUlid = $this->commandBus->execute(new SetPOINaviDataCommand(
            $ulid,
            $naviData,
            $input->mapUlid,
        ));
        return $this->json(new SetPOINaviDataOutput($poiUlid));
    }
}