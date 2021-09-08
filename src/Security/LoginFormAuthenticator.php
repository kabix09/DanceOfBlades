<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const FORM_NAME = 'login_form';
    public const FORM_CSRF = '_csrf';
    public const USER_EMAIL = 'email';
    public const USER_PASS = 'password';

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;
    /**
     * @var string
     */
    private $appCsrfToken;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $userPasswordEncoder, CsrfTokenManagerInterface $csrfTokenManager, RouterInterface $router, string $appCsrfToken)
    {
        $this->userRepository = $userRepository;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->appCsrfToken = $appCsrfToken;
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        if(!array_key_exists(self::USER_EMAIL, $request->request->get(self::FORM_NAME)) || !array_key_exists(self::USER_PASS, $request->request->get(self::FORM_NAME))) {
            throw new CustomUserMessageAuthenticationException('Missing form data');
        }

        return [
            self::USER_EMAIL => $request->request->get(self::FORM_NAME)[self::USER_EMAIL],
            self::USER_PASS => $request->request->get(self::FORM_NAME)[self::USER_PASS],
            self::FORM_CSRF => $request->request->get(self::FORM_NAME)[self::FORM_CSRF]
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if(!$this->csrfTokenManager->isTokenValid(new CsrfToken($this->appCsrfToken, $credentials[self::FORM_CSRF]))) {
            throw new InvalidCsrfTokenException('Invalid CSRF token');
        }

        $result = $this->userRepository->findOneBy([self::USER_EMAIL => $credentials[self::USER_EMAIL]]);
        if(is_null($result)) {
            throw new CustomUserMessageAuthenticationException('User not found - Invalid email');
        }

        return $result;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->userPasswordEncoder->isPasswordValid($user, $credentials[self::USER_PASS]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /** @var User $loginUser */
        $loginUser = $token->getUser();

        if(!$loginUser->getIsActive())
            return new RedirectResponse($this->router->generate('app_user_inactive_account'));

        $loginUser->setLastLoginDate(new \DateTime('now'));
        $this->entityManager->persist($loginUser);
        $this->entityManager->flush();

        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey))
        {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_user_profile'));
    }

//    public function start(Request $request, AuthenticationException $authException = null)
//    {
//        // add a custom flash message and redirect to the login page
//        $this->session->getFlashBag()->add('note', 'You have to login in order to access this page.');
//
//        return new RedirectResponse($this->router->generate('app_login'));
//    }


    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }
}
