<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\dto\RegisterUserModel;
use App\Form\LoginFormType;
use App\Form\RegisterFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use SodiumException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param EntityManagerInterface $entityManager
     * @param MailerInterface $mailer
     * @return Response
     * @throws SodiumException
     * @throws TransportExceptionInterface
     */
    public function register(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(RegisterFormType::class, new RegisterUserModel());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
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

            // generate unique token
            $newVerifyKey = sodium_crypto_generichash(
                (new \DateTime())->getTimestamp() . random_bytes(32),
                $this->emailVerifyKey
            );

            $newUser->setActivateKey(
                sodium_bin2hex($newVerifyKey)
            );

            // send an email
            $registerEmail = (new TemplatedEmail())
                ->from(new Address('kabix.009@gmail.com', 'kabix009'))
                ->to(new Address($newUser->getEmail(), $newUser->getNick()))
                ->subject('Registration email')
                ->htmlTemplate('email/registerUser.html.twig')
                ->context([
                    'activateToken' => sodium_bin2hex($newVerifyKey)
                ]);

            $mailer->send($registerEmail);

            // save user object in db
            $entityManager->persist($newUser);
            $entityManager->flush();

            return new Response("<html><head></head><body>Email was send successful. Please check your email in purpoe to activate your account :)</body></html>");
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
        $userRepository = $entityManager->getRepository(User::class);
        $matchUser = $userRepository->findOneBy(['activateKey' => $token]);

        if(!$matchUser) {
            throw new \Exception('Token not found');
        }

        $matchUser->setActivateKey('');
        $matchUser->setIsActive(true);

        $entityManager->persist($matchUser);
        $entityManager->flush();

        return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
            $matchUser,
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
