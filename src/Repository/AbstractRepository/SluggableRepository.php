<?php

namespace App\Repository\AbstractRepository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class SluggableRepository extends ServiceEntityRepository
{
    public function getNames(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.name')
            ->getQuery()
            ->getResult();
    }
}