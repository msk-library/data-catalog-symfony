<?php

namespace App\Repository;

use App\Entity\DataCollectionInstrument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DataCollectionInstrument>
 *
 * @method DataCollectionInstrument|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataCollectionInstrument|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataCollectionInstrument[]    findAll()
 * @method DataCollectionInstrument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataCollectionInstrumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DataCollectionInstrument::class);
    }

    public function add(DataCollectionInstrument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DataCollectionInstrument $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DataCollectionInstrument[] Returns an array of DataCollectionInstrument objects
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

//    public function findOneBySomeField($value): ?DataCollectionInstrument
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
