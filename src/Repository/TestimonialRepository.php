<?php

namespace App\Repository;

use App\Entity\Testimonial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TestimonialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Testimonial::class);
    }

    /** Retourne les 3 témoignages mis en avant, triés par position */
    public function findFeatured(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.isFeatured = true')
            ->orderBy('t.featuredPosition', 'ASC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }
}
