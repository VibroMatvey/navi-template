<?php

namespace App\Navi\Domain\Repository;

use App\Navi\Domain\Entity\POI;

interface POIRepositoryInterface
{
    public function save(POI $poi): void;
    public function remove(POI $poi): void;
    public function findPOIs(?string $mapUlid): array;
}