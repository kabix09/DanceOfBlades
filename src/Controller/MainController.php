<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Menu\MenuMapper;
use App\Repository\AvatarRepository;
use App\Repository\MenuRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('base\index.html.twig');
    }

    /**
     * @Route("/blankpage", name="app_blank_page")
     * @return Response
     */
    public function blankPage()
    {
        return $this->render('base/blank.html.twig');
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/user/profile", name="app_user_profile")
     * @param AvatarRepository $avatarRepository
     * @return Response
     */
    public function profile(AvatarRepository $avatarRepository): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if(!$user->getIsActive())
        {
            return $this->render('user/inactiveAccount.html.twig');
        }

        /**
         * @var Avatar $avatar
         */
        $avatar = $avatarRepository->findOneBy(['user' => $user]);
        if(is_null($avatar))
        {
            return $this->redirectToRoute('app_new_avatar');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'avatar' => $avatar,
            'isAdmin' => in_array("ROLE_ADMIN", $this->getUser()->getRoles(),true),
        ]);
    }

    /**
     * @Route("/menu/{menu}", name="menu")
     * @param string $menu
     * @param RouterInterface $router
     * @return RedirectResponse
     */
    public function menu(string $menu, RouterInterface $router, MenuMapper $menuMapper)
    {
        $route = $menuMapper->mapRoute($menu);

        if(array_key_exists($route, iterator_to_array($router->getRouteCollection()->getIterator())))
        {
            return $this->redirectToRoute($route);
        }

        return $this->redirectToRoute('app_blank_page');
    }
}
