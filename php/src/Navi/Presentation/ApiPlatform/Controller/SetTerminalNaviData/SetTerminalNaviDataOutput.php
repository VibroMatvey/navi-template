<?php

namespace App\Navi\Presentation\ApiPlatform\Controller\SetTerminalNaviData;

final readonly class SetTerminalNaviDataOutput
{
    public function __construct(
        public string $poiUlid,
    )
    {
    }
}