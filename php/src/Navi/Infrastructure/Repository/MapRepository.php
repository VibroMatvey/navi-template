<?php

namespace App\Navi\Infrastructure\Repository;

use App\Navi\Domain\Entity\Map;
use App\Navi\Domain\Repository\MapRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MapRepository extends ServiceEntityRepository implements MapRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Map::class);
    }
}