<?php

namespace App\Navi\Presentation\ApiPlatform\Controller\SetPOINaviData;

final readonly class SetPOINaviDataOutput
{
    public function __construct(
        public string $poiUlid,
    )
    {
    }
}