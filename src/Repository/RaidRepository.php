<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Raid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
* @method Raid|null find($id, $lockMode = null, $lockVersion = null)
* @method Raid|null findOneBy(array $criteria, array $orderBy = null)
* @method Raid[]    findAll()
* @method Raid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
*/
class RaidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Raid::class);
    }
}