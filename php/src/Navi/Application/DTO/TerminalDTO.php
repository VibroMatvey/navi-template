<?php

namespace App\Navi\Application\DTO;

use App\Navi\Domain\Entity\Terminal;

final readonly class TerminalDTO
{
    public function __construct(
        public string  $ulid,
        public string  $name,
        public ?string $mapUlid,
        public ?array  $naviData,
    )
    {
    }

    public static function fromEntity(Terminal $terminal): self
    {
        return new self(
            $terminal->getUlid(),
            $terminal->getName(),
            $terminal->getMap()?->getUlid(),
            $terminal->getNaviData(),
        );
    }
}