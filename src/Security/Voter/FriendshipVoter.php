<?php

namespace App\Security\Voter;

use App\Entity\Friendship;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class FriendshipVoter extends Voter
{
    private const FRIENDSHIP_VOTE = 'FRIENDSHIP_MANAGE';
    /**
     * @var Security
     */
    private Security $security;

    /**
     * FriendshipVoter constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return self::FRIENDSHIP_VOTE === $attribute
            && $subject instanceof Friendship;
    }

    /**
     * @param string $attribute
     * @param Friendship $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::FRIENDSHIP_VOTE:

                /* if user is logged and user's avatar is one of friendship's members then grant access*/
                if($this->security->isGranted("ROLE_USER") &&
                    ($subject->getRequester() === $user->getAvatar()[0] || $subject->getAddressee() === $user->getAvatar()[0])
                ) {
                    return true;
                }

                break;
        }

        return false;
    }
}
