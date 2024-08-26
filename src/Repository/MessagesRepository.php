<?php

// src/Repository/MessagesRepository.php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Messages;

class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class); // Add this line
    }

    public function findBySender($id, $uid)
    {
        $queryBuilder = $this->createQueryBuilder('m')
            ->select('m, u')
            ->join('m.sender', 'u')
            ->where('m.sender = :id OR m.receiver = :uid')
            ->setParameter('id', $id)
            ->setParameter('uid', $uid)
            ->orderBy('m.created', 'DESC');

        return $queryBuilder->getQuery()->getResult();
    }

}
