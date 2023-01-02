<?php

namespace App\Repository;

use App\Entity\SubjectPopulationAge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SubjectPopulationAge>
 *
 * @method SubjectPopulationAge|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubjectPopulationAge|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubjectPopulationAge[]    findAll()
 * @method SubjectPopulationAge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectPopulationAgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubjectPopulationAge::class);
    }

    public function add(SubjectPopulationAge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SubjectPopulationAge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SubjectPopulationAge[] Returns an array of SubjectPopulationAge objects
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

//    public function findOneBySomeField($value): ?SubjectPopulationAge
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
