<?php

namespace App\Repository;

use App\Entity\Avatar;
use App\Entity\Friendship;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    /**
     * @var Security
     */
    private Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Friendship::class);
        $this->security = $security;
    }

    public function findFriends(Avatar $avatar) : array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.addressee = :val OR f.requester = :val')
            ->andWhere('f.acceptedDate IS NOT NULL')
            ->andWhere('f.deletedDate IS NULL')
            ->join('f.addressee', 'ad')
            ->join('f.requester', 'rq')
            ->setParameter('val', $avatar)
            ->getQuery()
            ->getResult();
    }

    public function findInvitations(Avatar $avatar) : array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.addressee = :val')
            ->andWhere('f.acceptedDate IS NULL')
            ->andWhere('f.rejectedDate IS NULL')
            ->join('f.addressee', 'ad')
            ->join('f.requester', 'rq')
            ->setParameter('val', $avatar)
            ->getQuery()
            ->getResult();
    }

    public function isFriendshipExists(Avatar $avatar)
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $loggedAvatar = $user->getAvatar();

        return $this->createQueryBuilder('isFriend')
            ->andWhere('isFriend.requester = :loggedAvatar AND isFriend.addressee = :otherAvatar')
            ->orWhere('isFriend.requester = :otherAvatar AND isFriend.addressee = :loggedAvatar')
            ->orderBy('isFriend.sentDate', 'DESC')
            ->setParameter('loggedAvatar', $loggedAvatar)
            ->setParameter('otherAvatar', $avatar)
            ->getQuery()
            ->getResult()
            ;
    }
}
