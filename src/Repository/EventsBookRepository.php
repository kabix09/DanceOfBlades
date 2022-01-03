<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventsBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventsBook|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventsBook|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventsBook[]    findAll()
 * @method EventsBook[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventsBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventsBook::class);
    }

    public function paginationList()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('a')
            ->andWhere('a.type != :pvp')
            ->andWhere('a.type != :bossRaid')
            ->setParameter('pvp', 'PVP')
            ->setParameter('bossRaid', 'BOSS_RAID');
    }

    public function paginationTournamentsList()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('a')
            ->andWhere('a.type = :tournament')
            ->setParameter('tournament', 'TOURNAMENT');
    }

    public function paginationRaidsList()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('a')
            ->andWhere('a.type = :raid')
            ->setParameter('raid', 'RAID');
    }
}