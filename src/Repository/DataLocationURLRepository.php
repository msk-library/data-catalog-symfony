<?php

namespace App\Repository;

use App\Entity\DataLocationURL;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataLocationURL>
 *
 * @method DataLocationURL|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataLocationURL|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataLocationURL[]    findAll()
 * @method DataLocationURL[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataLocationURLRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataLocationURL::class);
    }

    public function add(DataLocationURL $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DataLocationURL $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DataLocationURL[] Returns an array of DataLocationURL objects
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

//    public function findOneBySomeField($value): ?DataLocationURL
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
