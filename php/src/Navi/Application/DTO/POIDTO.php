<?php

namespace App\Navi\Application\DTO;

use App\Navi\Domain\Entity\POI;

final readonly class POIDTO
{
    public function __construct(
        public string  $ulid,
        public string  $title,
        public ?string $mapUlid,
        public ?array  $naviData,
    )
    {
    }

    public static function fromEntity(POI $poi): self
    {
        return new self(
            $poi->getUlid(),
            $poi->getTitle(),
            $poi->getMap()?->getUlid(),
            $poi->getNaviData(),
        );
    }
}