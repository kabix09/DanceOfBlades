<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Selection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Selection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Selection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Selection[]    findAll()
 * @method Selection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SelectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Selection::class);
    }

    public function getAvatarRaces()
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.type = :val')
            ->setParameter('val', 'AVATAR_RACE')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAvatarClass()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :val')
            ->setParameter('val', 'AVATAR_CLASS')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMapTerrains()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->setParameter('val', 'MAP_TERRAIN')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMapTerrainsByParent(string $parentId)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->andWhere('t.dependencyTag = :p OR t.dependencyTag is null')
            ->setParameter('val', 'MAP_TERRAIN')
            ->setParameter('p', $parentId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMapAreas()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->setParameter('val', 'MAP_AREA')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getMapClimates()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->setParameter('val', 'MAP_CLIMATE')
            ->addOrderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getEventsTypes()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->setParameter('val', 'EVENT_TYPE')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAllRaces()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('(t.type = :val_1) OR (t.type = :val_2)')
            ->setParameter('val_1', 'RACE')
            ->setParameter('val_2', 'AVATAR_RACE')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getItemGroups()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->setParameter('val', 'ITEM_GROUP')
            ->addOrderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getItemTypes()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->setParameter('val', 'ITEM_TYPE')
            ->addOrderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getItemTypesByParent(string $parentId)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.type = :val')
            ->andWhere('t.dependencyTag = :p OR t.dependencyTag is null')
            ->setParameter('val', 'ITEM_TYPE')
            ->setParameter('p', $parentId)
            ->getQuery()
            ->getResult()
            ;
    }
}