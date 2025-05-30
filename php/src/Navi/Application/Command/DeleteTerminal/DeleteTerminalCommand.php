<?php

namespace App\Navi\Application\Command\DeleteTerminal;

use App\Shared\Application\Command\CommandInterface;

final readonly class DeleteTerminalCommand implements CommandInterface
{
    public function __construct(
        public string $terminalUlid,
    )
    {
    }
}