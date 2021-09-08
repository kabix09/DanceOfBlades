<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserKeys;
use App\Form\Dto\RegisterUserModel;
use App\Form\LoginFormType;
use App\Form\RegisterFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\Director\UserDirector;
use App\Service\Mailer;
use App\Service\Token;
use App\Service\Builder\UserKeyBuilder;
use App\Service\Builder\UserBuilder;
use App\Service\Director\UserKeyDirector;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var string
     */
    private $emailVerifyKey;

    public function __construct(string $emailVerifyKey)
    {
        $this->emailVerifyKey = $emailVerifyKey;
    }

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
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param UserDirector $userDirector
     * @param UserKeyDirector $userKeyDirector
     * @param Token $token
     * @param Mailer $mailer
     * @return Response
     */
    public function register(Request $request, AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager, UserRepository $userRepository, UserDirector $userDirector, UserKeyDirector $userKeyDirector, Token $token, Mailer $mailer): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(RegisterFormType::class, new RegisterUserModel());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $newUser = $userDirector->buildRegisterUser($form->getData());

            /*
             * generate unique token
             */
            $newVerifyKey = $token->generate($this->emailVerifyKey)->convertToHex();

            /*
             * exec transaction - create user & key
             */
            $entityManager->transactional(function ($entityManager) use ($newUser, $userKeyDirector, $newVerifyKey, $userRepository) {
                $entityManager->persist($newUser);
                $entityManager->flush();

                /*
                 * * prepare account activation token
                 */
                $newKey = $userKeyDirector->buildActivateAccount(
                    $newVerifyKey,
                    $userRepository->findOneBy(['email' => $newUser->getEmail()])
                );


                $entityManager->persist($newKey);
                $entityManager->flush();
            });

            /*
             * * email verification section
             */
            $mailer->sendWelcomeMessage($newUser, $newVerifyKey);


            return $this->render('email/afterSendEmailFeedback.html.twig');
        }

        return $this->render('form/user/register.html.twig', [
            'registerForm' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * @Route("/user/activateAccount/{token}", name="app_user_activate_account")
     * @param string $token
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @return Response|null
     * @throws \Exception
     */
    public function activateAccount(string $token, EntityManagerInterface $entityManager, Request $request, GuardAuthenticatorHandler $guardAuthenticatorHandler, LoginFormAuthenticator $loginFormAuthenticator): ?Response
    {
        $keysRepository = $entityManager->getRepository(UserKeys::class);
        $matchKey = $keysRepository->findOneBy(['value' => $token, 'type' => 'ACTIVATE_ACCOUNT']);

        if(!$matchKey) {
            throw new \RuntimeException('Token not found');
        }

        if($matchKey->getExpirationDate() < new \DateTime('now'))
        {
            throw new \RuntimeException('Token expired');
        }

        //activate user account
        $user = $matchKey->getUser();
        $user->setIsActive(true);

        // drop activation key
        $entityManager->remove($matchKey);
        $entityManager->flush();


        return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $loginFormAuthenticator,
            'main'
        );
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // todo
    }
}
