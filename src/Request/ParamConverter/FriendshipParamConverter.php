<?php
declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Entity\Friendship;
use App\Repository\AvatarRepository;
use App\Repository\FriendshipRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class FriendshipParamConverter implements ParamConverterInterface
{
    private const FRIENDSHIP_CONVERT_CASES = ['removedFriendship', 'acceptedInvitation', 'deniedInvitation'];
    /**
     * @var FriendshipRepository
     */
    private FriendshipRepository $friendshipRepository;
    /**
     * @var AvatarRepository
     */
    private AvatarRepository $avatarRepository;

    /**
     * FriendshipParamConverter constructor.
     * @param FriendshipRepository $friendshipRepository
     * @param AvatarRepository $avatarRepository
     */
    public function __construct(FriendshipRepository $friendshipRepository, AvatarRepository $avatarRepository)
    {
        $this->friendshipRepository = $friendshipRepository;
        $this->avatarRepository = $avatarRepository;
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $options = $configuration->getOptions()['mapping'];

        if (!isset($options['requester'], $options['addressee'])) {
            throw new BadRequestHttpException('Requester or addressee not provided in request.');
        }

        // handle avatars by nicks
        $requesterAvatar = $this->avatarRepository->findOneBy([
            'nick' => $request->attributes->get($options['requester'])
        ]);

        $addresseeAvatar = $this->avatarRepository->findOneBy([
            'nick' => $request->attributes->get($options['addressee'])
        ]);

        if(!$requesterAvatar || !$addresseeAvatar)
        {
            throw new BadCredentialsException(sprintf('Friendship between %s and %s don\'t exist',
                $request->attributes->get($options['requester']),
                $request->attributes->get($options['addressee'])
            ));
        }

        // if exists, fetch relations between avatars
        $actualFriendship = $this->friendshipRepository->findBy([
            'requester' => $requesterAvatar,
            'addressee' => $addresseeAvatar
            ],
            ['sentDate' => 'DESC']
        );

        // push latest friendship handled at first (0) place in array ordered desc by sent_date
        $request->attributes->set($configuration->getName(), $actualFriendship[0]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration)
    {
        if(in_array($configuration->getName(), self::FRIENDSHIP_CONVERT_CASES) && $configuration->getClass() === Friendship::class)
        {
            return true;
        }

        return false;
    }
}