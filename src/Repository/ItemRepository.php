<?php

namespace App\Repository;

use App\Entity\Item;
use App\Repository\AbstractRepository\SluggableRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends SluggableRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function paginationWeaponList()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('a')
            ->andWhere('a.group = :group')
            ->setParameter('group', 'Weapon');
    }

    public function paginationOutfitList()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('a')
            ->andWhere('a.group = :group')
            ->setParameter('group', 'Outfit');
    }

    public function paginationMagicItemsList()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('a')
            ->andWhere('a.group = :group')
            ->setParameter('group', 'Magic item');
    }

    public function paginationOtherList()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('a')
            ->andWhere('a.group = :group')
            ->setParameter('group', 'Other');
    }
}
