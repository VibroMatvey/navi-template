<?php

namespace App\Navi\Application\Command\DeleteTerminal;

use App\Navi\Domain\Repository\TerminalRepositoryInterface;
use App\Navi\Domain\Service\NaviServiceInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use RuntimeException;

final readonly class DeleteTerminalHandler implements CommandHandlerInterface
{
    public function __construct(
        private TerminalRepositoryInterface $repository,
        private NaviServiceInterface        $naviService,
    )
    {
    }

    public function __invoke(
        DeleteTerminalCommand $command
    ): void
    {
        $poi = $this->repository->find($command->terminalUlid);
        if (!$poi) {
            throw new RuntimeException(
                'terminal not found',
            );
        }
        if ($poi->getNaviData()) {
            $this->naviService->deleteTerminal($poi->getNaviData()['terminalId']);
        }
        $this->repository->remove($poi);
    }
}