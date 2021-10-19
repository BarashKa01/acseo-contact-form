<?php

namespace App\Repository;

use App\Entity\UserRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserRequest[]    findAll()
 * @method UserRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRequest::class);
    }

    public function findAllSorted(){
        return $this->createQueryBuilder('query')
            ->orderBy('query.usermail', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
