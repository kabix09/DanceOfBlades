<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserKeys;
use App\Event\NewAccountCreatedEvent;
use App\Form\Dto\RegisterUserModel;
use App\Form\LoginFormType;
use App\Form\RegisterFormType;
use App\Repository\UserKeysRepository;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\Director\UserDirector;
use App\Service\Mailer;
use App\Service\Token;
use App\Service\Director\UserKeyDirector;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
        if($this->isGranted("ROLE_USER"))
        {
            return $this->redirectToRoute('app_user_profile');
        }

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
    public function register(Request $request, AuthenticationUtils $authenticationUtils, EntityManagerInterface $entityManager, UserRepository $userRepository, UserDirector $userDirector, UserKeyDirector $userKeyDirector, Token $token, EventDispatcherInterface $eventDispatcher): Response
    {
        /*
         * if not granted role then user is not logged
         */
        if($this->isGranted("ROLE_USER"))
        {
            return $this->redirectToRoute('app_user_profile');
        }

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

            $eventDispatcher->dispatch(new NewAccountCreatedEvent($newUser, $newVerifyKey), NewAccountCreatedEvent::NAME);

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
     * @param UserKeysRepository $userKeysRepository
     * @return Response|null
     */
    public function activateAccount(string $token, EntityManagerInterface $entityManager, Request $request, GuardAuthenticatorHandler $guardAuthenticatorHandler, LoginFormAuthenticator $loginFormAuthenticator, UserKeysRepository $userKeysRepository): ?Response
    {
        $foundKey = $userKeysRepository->findOneBy(['value' => $token, 'type' => 'ACTIVATE_ACCOUNT']);

        if(!$foundKey) {
            throw new \RuntimeException('Token not found');
        }

        $key = null;
        foreach ($userKeysRepository->findBy(['user' => $foundKey->getUser()->getId(), 'type' => 'ACTIVATE_ACCOUNT']) as $matchedKey)
        {
            if($key === null && $matchedKey->getExpirationDate() > new \DateTime('now'))
            {
                $key = $matchedKey;
            }

            // drop activation key
            $entityManager->remove($matchedKey);
        }

        if($key === null)
            throw new \RuntimeException('Token expired');

        //activate user account
        $user = $key->getUser();
        $user->setIsActive(true);

        $entityManager->flush();

        return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $loginFormAuthenticator,
            'main'
        );
    }


    /**
     * @param EntityManagerInterface $entityManager
     * @param Mailer $mailer
     * @param Token $token
     * @param UserKeyDirector $userKeyDirector
     * @param UserRepository $userRepository
     * @IsGranted("ROLE_USER")
     * @Route("/user/activateAccountEmail", name="app_user_sent_activate_email_again")
     * @return Response
     */
    public function sendActivateEmail(EntityManagerInterface $entityManager, Mailer $mailer, Token $token, UserKeyDirector $userKeyDirector, UserRepository $userRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        /*
         * * generate unique token
         */
        $newVerifyKey = $token->generate($this->emailVerifyKey)->convertToHex();

        /*
         * * prepare account activation token
         */
        $newKey = $userKeyDirector->buildActivateAccount(
            $newVerifyKey,
            $userRepository->findOneBy(['email' => $user->getEmail()])
        );

        $entityManager->persist($newKey);
        $entityManager->flush();

        /*
         * * email verification section
         */
        $mailer->sendWelcomeMessage($user, $newVerifyKey);

        return $this->render('email/afterSendEmailFeedback.html.twig');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        // todo
    }
}
