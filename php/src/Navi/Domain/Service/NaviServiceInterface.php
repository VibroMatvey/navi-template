<?php

namespace App\Navi\Domain\Service;

interface NaviServiceInterface
{
    public function deleteArea(int $areaId): void;

    public function deletePoint(int $pointId): void;

    public function deleteTerminal(int $terminalId): void;
}