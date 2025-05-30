<?php

namespace App\Navi\Domain\Repository;

use App\Navi\Domain\Entity\Terminal;

interface TerminalRepositoryInterface
{
    public function save(Terminal $terminal): void;
    public function remove(Terminal $terminal): void;
    public function findTerminals(?string $mapUlid): array;
}