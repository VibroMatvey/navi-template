<?php

namespace App\Navi\Application\Command\SetPOINaviData;

use App\Shared\Application\Command\CommandInterface;

final readonly class SetPOINaviDataCommand implements CommandInterface
{
    public function __construct(
        public string  $poiUlid,
        public ?array  $naviData,
        public ?string $mapUlid,
    )
    {
    }
}