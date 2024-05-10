<?php

namespace App\Repository;

use App\Entity\SanPham;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<SanPham>
 *
 * @method SanPham|null find($id, $lockMode = null, $lockVersion = null)
 * @method SanPham|null findOneBy(array $criteria, array $orderBy = null)
 * @method SanPham[]    findAll()
 * @method SanPham[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SanPhamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SanPham::class);
    }

    public function save(SanPham $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SanPham $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByKeyword($keyword)
    {
        return $this->createQueryBuilder('s')
            ->where('s.name LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->getQuery()
            ->getResult();
    }
    
    public function findByKeywordWithLimit($keyword,$limit=5,$page=1)
    {
        return $this->createQueryBuilder('s')
            ->where('s.name LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%')
            ->setMaxResults($limit)
            ->setFirstResult(($page-1)*$limit)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return SanPham[] Returns an array of SanPham objects
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

//    public function findOneBySomeField($value): ?SanPham
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
