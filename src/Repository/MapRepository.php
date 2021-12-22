<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Map;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Map|null find($id, $lockMode = null, $lockVersion = null)
 * @method Map|null findOneBy(array $criteria, array $orderBy = null)
 * @method Map[]    findAll()
 * @method Map[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Map::class);
    }

    public function getMapsNames(): array
    {
        return $this->createQueryBuilder('m')
            ->select('m.name')
            ->getQuery()
            ->getResult();
    }

    public function paginationList()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('a');
    }
}