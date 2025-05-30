<?php

namespace App\Navi\Presentation\ApiPlatform\Controller\SetPOINaviData;

use App\Navi\Application\DTO\NaviData\Area;
use App\Navi\Application\DTO\NaviData\Point;

final readonly class SetPOINaviDataInput
{
    public function __construct(
        public ?Area   $area,
        public ?Point  $point,
        public ?string $mapUlid,
    )
    {
    }
}