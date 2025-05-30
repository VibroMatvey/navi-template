<?php

namespace App\Navi\Presentation\ApiPlatform\Controller\SetTerminalNaviData;

use App\Navi\Application\DTO\NaviData\TerminalPoint;

final readonly class SetTerminalNaviDataInput
{
    public function __construct(
        public ?TerminalPoint $point,
        public ?string        $mapUlid,
    )
    {
    }
}