<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserKeys;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserKeys|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserKeys|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserKeys[]    findAll()
 * @method UserKeys[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserKeysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserKeys::class);
    }
}