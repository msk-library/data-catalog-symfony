<?php

namespace App\Repository;

use App\Entity\SubjectGeographicArea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubjectGeographicArea>
 *
 * @method SubjectGeographicArea|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubjectGeographicArea|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubjectGeographicArea[]    findAll()
 * @method SubjectGeographicArea[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectGeographicAreaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubjectGeographicArea::class);
    }

    public function add(SubjectGeographicArea $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SubjectGeographicArea $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SubjectGeographicArea[] Returns an array of SubjectGeographicArea objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SubjectGeographicArea
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
