<?php

namespace App\Navi\Application\Command\SetTerminalNaviData;

use App\Shared\Application\Command\CommandInterface;

final readonly class SetTerminalNaviDataCommand implements CommandInterface
{
    public function __construct(
        public string  $terminalUlid,
        public ?array  $naviData,
        public ?string $mapUlid,
    )
    {
    }
}