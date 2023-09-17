<?php

namespace App\Repository;

use App\Entity\Leave;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\Paginator;

/**
 * @extends ServiceEntityRepository<Leave>
 *
 * @method Leave|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leave|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leave[]    findAll()
 * @method Leave[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Leave::class);
    }

    public function findAllWithPagination(int $page, int $itemsPerPage) : Paginator
    {
        $query = $this->createQueryBuilder('t')->orderBy('t.createdAt', 'DESC');

        return new Paginator($query, $page, $itemsPerPage);
    }

    public function findByStatusAndSort(string $status, int $page, int $itemsPerPage, string $sortBy = 'ASC') : Paginator
    {
        $query = $this->createQueryBuilder('t');

        if($status == 'all'){
            $query->orderBy('t.startAt', $sortBy);
        }else{
            $query->andWhere('t.status = :val')->setParameter('val', $status)->orderBy('t.startAt', $sortBy);
        }

        return new Paginator($query, $itemsPerPage, $page);
    }

    public function findByTeamId(int $teamId, string $status, int $page, int $itemsPerPage, string $sortBy = 'ASC') : Paginator
    {
        $query = $this->createQueryBuilder('l')
                ->innerJoin('l.owner', 'o')
                ->innerJoin('o.team', 't')
                ->where('t.id = :teamId')
                ->setParameter('teamId', $teamId);
        
        if($status == 'all'){
            $query->orderBy('l.startAt', $sortBy);
        }else{
            $query->andWhere('l.status = :val')->setParameter('val', $status)->orderBy('l.startAt', $sortBy);
        }


        $query->getQuery()->getResult();

        return new Paginator($query, $itemsPerPage, $page);
    }

    public function findByUserId(int $id, string $status, int $page, int $itemsPerPage, string $sortBy = 'ASC') : Paginator
    {
        $query = $this->createQueryBuilder('l')
        ->join('l.owner', 'o')
        ->andWhere('o.id = :ownerId')
        ->setParameter('ownerId', $id);

        if($status == 'all'){
            $query->orderBy('l.startAt', $sortBy);
        }else{
            $query->andWhere('l.status = :val')->setParameter('val', $status)->orderBy('l.startAt', $sortBy);
        }

        return new Paginator($query, $itemsPerPage, $page);
    }


//    /**
//     * @return Leave[] Returns an array of Leave objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Leave
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
