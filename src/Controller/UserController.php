<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\User\ChangeEmailFormType;
use App\Form\User\ChangePasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var AuthenticationUtils
     */
    private AuthenticationUtils $authenticationUtils;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(RequestStack $requestStack, AuthenticationUtils $authenticationUtils, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->authenticationUtils = $authenticationUtils;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("user/changeEmail", name="app_user_change_email")
     */
    public function changeEmail()
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(ChangeEmailFormType::class);
        $form->handleRequest($this->request);

        if($form->isSubmitted() && $form->isValid())
        {
            $formData = $form->getData();

            /** @var User $loggedUser */
            $loggedUser = $this->getUser();

            $loggedUser->setEmail($formData['newEmail']);

            $this->entityManager->persist($loggedUser);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_user_profile');
        }
        return $this->render('form/user/changeEmail.html.twig', [
            'changeEmailForm' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("user/changePassword", name="app_user_change_password")
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function changePassword(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($this->request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var User $loggedUser */
            $loggedUser = $this->getUser();

            $formData = $form->getData();

            if($userPasswordEncoder->isPasswordValid(
                $loggedUser,
                $formData['oldPassword']
            ))
            {
                $loggedUser->setPassword(
                    $userPasswordEncoder->encodePassword(
                        $loggedUser,
                        $formData['newPassword']
                    )
                );


                $this->entityManager->persist($loggedUser);
                $this->entityManager->flush();

                return $this->redirectToRoute('app_user_profile');
            }

            $form->addError(new FormError('Incorrect current password'));
        }

        return $this->render('form/user/changePassword.html.twig', [
            'changePasswordForm' => $form->createView(),
            'error' => $error
        ]);
    }
}