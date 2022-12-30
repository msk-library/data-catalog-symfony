<?php

namespace App\Repository;

use App\Entity\DatasetAlternateTitle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DatasetAlternateTitle>
 *
 * @method DatasetAlternateTitle|null find($id, $lockMode = null, $lockVersion = null)
 * @method DatasetAlternateTitle|null findOneBy(array $criteria, array $orderBy = null)
 * @method DatasetAlternateTitle[]    findAll()
 * @method DatasetAlternateTitle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DatasetAlternateTitleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DatasetAlternateTitle::class);
    }

    public function add(DatasetAlternateTitle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DatasetAlternateTitle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DatasetAlternateTitle[] Returns an array of DatasetAlternateTitle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DatasetAlternateTitle
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
