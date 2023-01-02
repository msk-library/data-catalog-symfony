<?php

namespace App\Repository;

use App\Entity\RelatedSoftware;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RelatedSoftware>
 *
 * @method RelatedSoftware|null find($id, $lockMode = null, $lockVersion = null)
 * @method RelatedSoftware|null findOneBy(array $criteria, array $orderBy = null)
 * @method RelatedSoftware[]    findAll()
 * @method RelatedSoftware[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelatedSoftwareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RelatedSoftware::class);
    }

    public function add(RelatedSoftware $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RelatedSoftware $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RelatedSoftware[] Returns an array of RelatedSoftware objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RelatedSoftware
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
