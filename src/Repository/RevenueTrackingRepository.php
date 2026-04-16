<?php

namespace App\Repository;

use App\Entity\RevenueTracking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RevenueTracking>
 */
class RevenueTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevenueTracking::class);
    }

    /** @return RevenueTracking[] */
    public function findByPeriod(string $period): array
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.payment_date', 'DESC');

        $this->applyPeriodFilter($qb, $period);

        return $qb->getQuery()->getResult();
    }

    /** @return array{total: float, adrien: float, marco: float} */
    public function getStatsByPeriod(string $period): array
    {
        $qb = $this->createQueryBuilder('r')
            ->select(
                'COALESCE(SUM(r.amount_total), 0) as total',
                'COALESCE(SUM(r.share_adrien), 0) as adrien',
                'COALESCE(SUM(r.share_marco), 0) as marco'
            );

        $this->applyPeriodFilter($qb, $period);

        $result = $qb->getQuery()->getOneOrNullResult();

        return [
            'total'  => (float) ($result['total'] ?? 0),
            'adrien' => (float) ($result['adrien'] ?? 0),
            'marco'  => (float) ($result['marco'] ?? 0),
        ];
    }

    /** @return array{total: float, adrien: float, marco: float} */
    public function getStatsCurrentMonth(): array
    {
        return $this->getStatsByPeriod('month');
    }

    private function applyPeriodFilter(\Doctrine\ORM\QueryBuilder $qb, string $period): void
    {
        $now = new \DateTimeImmutable();

        $start = match ($period) {
            'day'   => $now->setTime(0, 0, 0),
            'week'  => $now->modify('monday this week')->setTime(0, 0, 0),
            'month' => $now->modify('first day of this month')->setTime(0, 0, 0),
            'year'  => $now->modify('first day of january this year')->setTime(0, 0, 0),
            default => $now->modify('first day of this month')->setTime(0, 0, 0),
        };

        $qb->andWhere('r.payment_date >= :start')
           ->setParameter('start', $start);
    }
}
