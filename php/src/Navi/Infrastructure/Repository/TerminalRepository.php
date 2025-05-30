<?php

namespace App\Navi\Infrastructure\Repository;

use App\Navi\Domain\Entity\Terminal;
use App\Navi\Domain\Repository\TerminalRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TerminalRepository extends ServiceEntityRepository implements TerminalRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terminal::class);
    }

    public function save(Terminal $terminal): void
    {
        $this->getEntityManager()->persist($terminal);
        $this->getEntityManager()->flush();
    }

    public function remove(Terminal $terminal): void
    {
        $this->getEntityManager()->remove($terminal);
        $this->getEntityManager()->flush();
    }

    public function findTerminals(?string $mapUlid): array
    {
        if (!$mapUlid) {
            return $this->findAll();
        }

        return $this->createQueryBuilder('t')
            ->leftJoin('t.map', 'tm')
            ->where('tm.ulid = :mapUlid')
            ->setParameter('mapUlid', $mapUlid)
            ->getQuery()
            ->getResult();
    }
}