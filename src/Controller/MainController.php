<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="base")
     */
    public function index(): Response
    {
        return $this->render('base\index.html.twig', [
            'controller_name' => 'MainController',
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
