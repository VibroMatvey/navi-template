<?php

namespace App\Navi\Infrastructure\Repository;

use App\Navi\Domain\Entity\POI;
use App\Navi\Domain\Repository\POIRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class POIRepository extends ServiceEntityRepository implements POIRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, POI::class);
    }

    public function save(POI $poi): void
    {
        $this->getEntityManager()->persist($poi);
        $this->getEntityManager()->flush();
    }

    public function remove(POI $poi): void
    {
        $this->getEntityManager()->remove($poi);
        $this->getEntityManager()->flush();
    }

    public function findPOIs(?string $mapUlid): array
    {
        if (!$mapUlid) {
            return $this->findAll();
        }

        return $this->createQueryBuilder('p')
            ->leftJoin('p.map', 'pm')
            ->where('pm.ulid = :mapUlid')
            ->setParameter('mapUlid', $mapUlid)
            ->getQuery()
            ->getResult();
    }
}