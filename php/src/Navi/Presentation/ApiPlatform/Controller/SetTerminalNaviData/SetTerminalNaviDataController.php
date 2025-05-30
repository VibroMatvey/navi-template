<?php

namespace App\Navi\Presentation\ApiPlatform\Controller\SetTerminalNaviData;

use App\Navi\Application\Command\SetTerminalNaviData\SetTerminalNaviDataCommand;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController, Route(path: '/api/terminals/{ulid}/navi', name: 'set_terminal_navi_data', methods: ['POST'])]
final class SetTerminalNaviDataController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    )
    {
    }

    public function __invoke(
        $ulid,
        #[MapRequestPayload] SetTerminalNaviDataInput $input
    ): JsonResponse
    {
        $naviData = null;
        if ($input->point) {
            $naviData = $input->point;
        }
        $poiUlid = $this->commandBus->execute(new SetTerminalNaviDataCommand(
            $ulid,
            $naviData,
            $input->mapUlid,
        ));
        return $this->json(new SetTerminalNaviDataOutput($poiUlid));
    }
}