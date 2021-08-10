<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\dto\RegisterUserModel;
use App\Form\LoginFormType;
use App\Form\RegisterFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class, new User());
        $form->handleRequest($request);

        return $this->render('form/user/login.html.twig', [
            'loginForm' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param EntityManagerInterface $entityManager
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @return Response
     */
    public function register(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, GuardAuthenticatorHandler $guardAuthenticatorHandler, LoginFormAuthenticator $loginFormAuthenticator)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(RegisterFormType::class, new RegisterUserModel());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // todo: send activated account email +  redirect to 'thank you' page

            /**
             * @var RegisterUserModel $formObject
             */
            $formObject = $form->getData();

            $newUser = new User();
            $newUser->setNick($formObject->getNick());
            $newUser->setEmail($formObject->getEmail());
            $newUser->setPassword(
                $userPasswordEncoder->encodePassword(
                    $newUser,
                    $formObject->getPassword()
                )
            );
            $newUser->setRoles(['ROLE_USER']);
            $newUser->setAcceptTermsDate(new \DateTime("now"));
            $newUser->setCreateAccountDate(new \DateTime("now"));

            $newUser->setIsActive(false);

            $entityManager->persist($newUser);
            $entityManager->flush();

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $newUser,
                $request,
                $loginFormAuthenticator,
                'main'
            );
        }

        return $this->render('form/user/register.html.twig', [
            'registerForm' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // todo
    }
}
