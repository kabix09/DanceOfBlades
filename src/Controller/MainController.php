<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\User;
use App\Repository\AvatarRepository;
use App\Repository\MenuRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(): Response
    {
        return $this->render('base\index.html.twig');
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
     * @param MenuRepository $menuRepository
     */
    public function menu(string $menu, MenuRepository $menuRepository)
    {
//        if(in_array($menu,
//            array_reduce(
//                $menuRepository->findAll(),
//                function (array $response, Menu $menu) {
//                    $response[] = $menu->getCategory();
//                    return $response;
//                },
//                []
//            ), true)
//        ) {
//            return new Response(
//                '<html><body>Current page: ' . $menu . '</body></html>'
//            );
//        }
//        else
//            return $this->redirectToRoute('app_login');
//            //return new Response("<body>Not found {$menu}</body>");
    }
}
