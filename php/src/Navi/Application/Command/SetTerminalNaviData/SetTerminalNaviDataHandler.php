<?php

namespace App\Navi\Application\Command\SetTerminalNaviData;

use App\Navi\Domain\Repository\MapRepositoryInterface;
use App\Navi\Domain\Repository\TerminalRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Exception\ApiPlatform\DomainApiPlatformException;

final readonly class SetTerminalNaviDataHandler implements CommandHandlerInterface
{
    public function __construct(
        private TerminalRepositoryInterface $repository,
        private MapRepositoryInterface      $mapRepository,
    )
    {
    }

    public function __invoke(
        SetTerminalNaviDataCommand $command
    ): string
    {
        $terminal = $this->repository->find($command->terminalUlid);
        if (!$terminal) {
            throw new DomainApiPlatformException(
                'terminal with ulid ' . $command->terminalUlid . ' not found',
                404,
                201
            );
        }
        $terminal->setNaviData($command->naviData);
        if ($command->mapUlid) {
            $map = $this->mapRepository->find($command->mapUlid);
            if (!$map) {
                throw new DomainApiPlatformException(
                    'map with ulid ' . $command->mapUlid . ' not found',
                    404,
                    202
                );
            }
            $terminal->setMap($map);
        } else {
            $terminal->setMap(null);
        }
        $this->repository->save($terminal);
        return $terminal->getUlid();
    }
}