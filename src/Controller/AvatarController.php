<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\Avatar\CreateAvatarFormType;
use App\Form\Dto\CreateAvatarModel;
use App\Repository\AvatarRepository;
use App\Service\Director\AvatarDirector;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    public function __construct(AvatarRepository $avatarRepository, RequestStack $requestStack)
    {
        $this->avatarRepository = $avatarRepository;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/avatar/new", name="app_new_avatar")
     * @param EntityManagerInterface $entityManager
     * @param AvatarDirector $avatarDirector
     * @return RedirectResponse|Response
     */

    public function new(EntityManagerInterface $entityManager, AvatarDirector $avatarDirector)
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
                // image
                $destinationPath = $this->getParameter('kernel.project_dir') . '/public/uploads/avatar';

                $avatarImage->move(
                    $destinationPath,
                    $newAvatar->getImage()
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
}
