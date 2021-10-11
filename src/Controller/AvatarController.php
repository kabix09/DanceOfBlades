<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Event\AvatarImageDeleteEvent;
use App\Event\NewAccountCreatedEvent;
use App\Event\AvatarImageUploadEvent;
use App\Form\Avatar\ChangeAvatarImageFormType;
use App\Form\Avatar\CreateAvatarFormType;
use App\Form\Dto\CreateAvatarModel;
use App\Repository\AvatarRepository;
use App\Repository\FriendshipRepository;
use App\Service\Director\AvatarDirector;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AvatarController extends AbstractController
{
    /**
     * @var AvatarRepository
     */
    private AvatarRepository $avatarRepository;
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param AvatarRepository $avatarRepository
     * @param RequestStack $request
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(AvatarRepository $avatarRepository, RequestStack $request, EventDispatcherInterface $eventDispatcher)
    {
        $this->avatarRepository = $avatarRepository;
        $this->request = $request->getCurrentRequest();
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/avatar", name="app_list_avatar")
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $this->avatarRepository->paginationList(),
            $this->request->query->getInt('page', 1),
            15
        );
        return $this->render('avatar/index.html.twig', [
            'avatars' => $pagination
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/avatar/new", name="app_new_avatar")
     * @param EntityManagerInterface $entityManager
     * @param AvatarDirector $avatarDirector
     * @param Uploader $uploader
     * @return RedirectResponse|Response
     */
    public function new(EntityManagerInterface $entityManager, AvatarDirector $avatarDirector, Uploader $uploader)
    {
        if($this->avatarRepository->findOneBy(['user' => $this->getUser()]))  // if user has avatar then redirect to profile page
            return $this->redirectToRoute('app_user_profile');

        $form = $this->createForm(CreateAvatarFormType::class, new CreateAvatarModel());

        $form->handleRequest($this->request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Avatar $newAvatar */
            $newAvatar = $avatarDirector->newAvatar($form->getData());

            /** @Var UploadedFile | null $avatarImage */
            $avatarImage = $form->getData()->getImage();
            if($avatarImage) {

                $this->eventDispatcher->dispatch(
                    new AvatarImageUploadEvent($newAvatar->getImage(), $avatarImage),
                    AvatarImageUploadEvent::NAME
                );

            }

            $entityManager->persist($newAvatar);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('form/avatar/newAvatar.html.twig', [
            'avatarForm' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/avatar/changeImage", name="app_avatar_change_image")
     * @param EntityManagerInterface $entityManager
     * @param Uploader $uploader
     * @param AvatarDirector $avatarDirector
     * @param AuthenticationUtils $authenticationUtils
     * @return RedirectResponse|Response
     */
    public function newImage(EntityManagerInterface $entityManager, AvatarDirector $avatarDirector, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(ChangeAvatarImageFormType::class);
        $form->handleRequest($this->request);

        if($form->isSubmitted() && $form->isValid())
        {
            $formData = $form->getData();

            /** @var User $loggedUser */
            $loggedUser = $this->getUser();

            $avatar = $this->avatarRepository->findOneBy(['user' => $loggedUser]);

            if($avatar && $oldImage = $avatar->getImage())
            {
                $this->eventDispatcher->dispatch(
                    new AvatarImageDeleteEvent($oldImage),
                    AvatarImageDeleteEvent::NAME
                );
            }

            $avatarDirector->setImage($formData['newImage'], $avatar);

            $this->eventDispatcher->dispatch(
                new AvatarImageUploadEvent($avatar->getImage(),$formData['newImage']),
                AvatarImageUploadEvent::NAME
            );

            $entityManager->persist($avatar);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('form/avatar/changeAvatarImage.html.twig', [
            'changeImageForm' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("/avatar/invitations", name="avatar_invitations_mercure")
     * @param Request $request
     * @param Discovery $discovery
     * @return JsonResponse
     */
    public function avatarInvitations(Request $request, Discovery $discovery)
    {
        $discovery->addLink($request);

        return $this->json(['message' => 'get mercure hub link']);
    }

    /**
     * @Route("/avatar/{nick}", name="app_avatar_guest")
     * @IsGranted("ROLE_USER")
     * @param Avatar $avatar
     * @param FriendshipRepository $friendshipRepository
     * @return Response
     */
    public function guestAvatar(Avatar $avatar, FriendshipRepository $friendshipRepository): Response
    {
        $friendships = $friendshipRepository->isFriendshipExists($avatar);

        return $this->render('user/guestProfile.html.twig', [
            'avatar' => $avatar,
            'isAdmin' => in_array("ROLE_ADMIN", $this->getUser()->getRoles(),true),
            'isFriendship' => $friendships ? true : false,
            'friendship' => $friendships ? $friendships[0] : null   // pass the news friendship, oldest are historical eg deleted or rejected relations
        ]);
    }
}
