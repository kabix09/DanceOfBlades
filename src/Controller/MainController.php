<?php

namespace App\Controller;

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
     * @Route("/{menu}", name="menu")
     */
    public function menu(string $menu)
    {
        return new Response(
            '<html><body>Current page: ' . $menu . '</body></html>'
        );
    }
}
