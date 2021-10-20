<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use App\Repository\LogRepository;
use App\Types\MyDateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FriendsController
 * @package App\Controller
 *
 */
class FriendsController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/user/friends", name="app_user_friend")
     * @IsGranted("ROLE_USER")
     * @param FriendshipRepository $friendshipRepository
     * @param LogRepository $logRepository
     * @return Response
     */
    public function showFriends(FriendshipRepository $friendshipRepository, LogRepository $logRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $friendships = $friendshipRepository->findFriends($user->getAvatar());
        $notAcceptedInvitations = $friendshipRepository->findInvitations($user->getAvatar());

        // invitations where send time is grater than last login
        $logins = $logRepository->getUserLogsHistory($user);
        $previousLoginDate = count($logins) >= 2
            ? $logins[1]->getStartSessionDate()
            : $logins[0]->getStartSessionDate();

        $newInvitations = array_filter($notAcceptedInvitations, function ($invitation) use ($previousLoginDate) {
            return $invitation->getSentDate() > $previousLoginDate;
        });

        return $this->render('user/profile/friends.html.twig', [
            'friendships' => $friendships,
            'notAcceptedInvitations' => $notAcceptedInvitations,
            'newInvitations' => $newInvitations
        ]);
    }

    /**
     * @Route("/user/friend/addFriend/{nick}", name="app_friend_add")
     * @IsGranted("ROLE_USER")
     * @ParamConverter(name="invitedAvatar", options={"mapping": {"nick": "nick"}})
     * @param Avatar $invitedAvatar
     * @param FriendshipRepository $friendshipRepository
     * @param HubInterface $hub
     * @return RedirectResponse
     */
    public function addToFriends(Avatar $invitedAvatar, FriendshipRepository $friendshipRepository, HubInterface $hub)
    {
        // todo: sent invitation for queue (messenger - rabbitMQ)

        $friendship = $friendshipRepository->isFriendshipExists($invitedAvatar);

        if(empty($friendship) || !is_null($friendship[0]->getRejectedDate()) || !is_null($friendship[0]->getDeletedDate()))
        {
            $newFriendship = (new Friendship())
                ->setRequester($this->getUser()->getAvatar())
                ->setAddressee($invitedAvatar)
                ->setSentDate(new MyDateTime())
            ;

            $this->entityManager->persist($newFriendship);
            $this->entityManager->flush();


            $update = new Update(
                sprintf('http://localhost:8000/%s/invitations', $invitedAvatar->getNick()),
                $this->json($newFriendship, 200, [], [
                    'groups' => ['friendship', 'avatar'],
                ])->getContent()
            );

            $hub->publish($update);
        }

        return $this->redirectToRoute('app_user_profile');
    }

    // FRIENDSHIP_MANAGE voter additionally checks if logged user is one of the ones in relationship for each of belows methods

    /**
     * @Route("/user/friend/removefriend/{requester}/{addressee}", name="app_friend_remove")
     * @IsGranted("FRIENDSHIP_MANAGE", subject="removedFriendship")
     * @ParamConverter("removedFriendship", options={"mapping": {"requester": "requester", "addressee": "addressee"}})
     * @param Friendship $removedFriendship
     * @return RedirectResponse
     */
    public function removeFromFriends(Friendship $removedFriendship): RedirectResponse
    {
        // todo: sent invitation for queue (messenger - rabbitMQ)
        $removedFriendship
            ->setDeletedDate(new \DateTime())
        ;

        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_profile');
    }

    /**
     * @Route("/user/friend/acceptInvitation/{requester}/{addressee}", name="app_friend_accept_invitation")
     * @IsGranted("FRIENDSHIP_MANAGE", subject="acceptedInvitation")
     * @ParamConverter("acceptedInvitation", options={"mapping": {"requester": "requester", "addressee": "addressee"}})
     * @param Friendship $acceptedInvitation
     * @return RedirectResponse
     */
    public function acceptInvitation(Friendship $acceptedInvitation): RedirectResponse
    {
        // todo: sent invitation for queue (messenger - rabbitMQ)
        $acceptedInvitation
            ->setAcceptedDate(new \DateTime())
        ;

        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_profile');
    }

    /**
     * @Route("/user/friend/denyInvitation/{requester}/{addressee}", name="app_friend_reject_invitation")
     * @IsGranted("FRIENDSHIP_MANAGE", subject="deniedInvitation")
     * @ParamConverter("deniedInvitation", options={"mapping": {"requester": "requester", "addressee": "addressee"}})
     * @param Friendship $deniedInvitation
     * @return RedirectResponse
     */
    public function rejectInvitation(Friendship $deniedInvitation): RedirectResponse
    {
        // todo: sent invitation for queue (messenger - rabbitMQ)
        $deniedInvitation
            ->setRejectedDate(new \DateTime())
        ;

        $this->entityManager->flush();

        return $this->redirectToRoute('app_user_profile');
    }
}