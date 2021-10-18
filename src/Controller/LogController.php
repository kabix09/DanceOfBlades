<?php

namespace App\Controller;

use App\Repository\LogRepository;
use Ramsey\Collection\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    /**
     * @var LogRepository
     */
    private LogRepository $repository;

    public function __construct(LogRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("user/log", name="app_user_log")
     */
    public function index(): Response
    {
        /** @var Collection $logs */
        $logs = $this->repository->findBy([], ['startSessionDate' => 'DESC']);

        return $this->render('log/index.html.twig', [
            'logs' => $logs,
        ]);
    }
}
