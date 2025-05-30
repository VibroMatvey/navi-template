<?php

namespace App\Navi\Application\DTO;

use App\Navi\Domain\Entity\Map;
use Symfony\Component\Filesystem\Path;

final readonly class MapDTO
{
    public function __construct(
        public string $ulid,
        public string $name,
        public string $image,
    )
    {
    }

    public static function fromEntity(Map $map): self
    {
        return new self(
            $map->getUlid(),
            $map->getName(),
            Path::join(Map::UPLOAD_DIR, $map->getImage()),
        );
    }
}