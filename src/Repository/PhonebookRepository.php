<?php

namespace App\Repository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Carriers;

class PhonebookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, [
            Phonebook::class,
            Carriers::class
        ]);
    }

    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('c')
            ->from(Carriers::class, 'c')
            ->orderBy('c.id', 'DESC');

        $query = $qb->getQuery();
        return $query->getResult();
    }

    public function findOneById($sid)
    {
        return $this->findOneBy(['id' => $sid]);
    }
}