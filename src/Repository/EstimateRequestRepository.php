<?php

namespace App\Repository;

use App\Entity\EstimateRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EstimateRequest>
 */
class EstimateRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstimateRequest::class);
    }

    /** @return EstimateRequest[] */
    public function findPending(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :status')
            ->setParameter('status', 'en_attente')
            ->orderBy('e.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return EstimateRequest[] */
    public function findRecent(int $limit = 5): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countPending(): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.status = :status')
            ->setParameter('status', 'en_attente')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
