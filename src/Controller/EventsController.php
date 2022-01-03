<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Entity\EventsBook;
use App\Entity\Pvp;
use App\Entity\Raid;
use App\Entity\StoneOfFreedom;
use App\Entity\Tournament;
use App\Form\Event\EventFormType;
use App\Repository\EventsBookRepository;
use App\Repository\RaidRepository;
use App\Repository\TournamentRepository;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\RepositoryInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * RankingsController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /*
     * TODO: add button on page to use this route
     */
    /**
     * @Route("/events", name="app_events_list")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param EventsBookRepository $eventsRepository
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request, EventsBookRepository $eventsRepository): Response
    {
        $pagination = $paginator->paginate(
            $eventsRepository->paginationList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('event/list.html.twig', [
            'events' => $pagination
        ]);
    }

    /**
     * @Route("/tournaments", name="app_tournaments_list")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param TournamentRepository $tournamentRepository
     * @return Response
     */
    public function tournamentsList(PaginatorInterface $paginator, Request $request, EventsBookRepository $eventsRepository)
    {
        $pagination = $paginator->paginate(
            $eventsRepository->paginationTournamentsList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('event/list.html.twig', [
            'events' => $pagination
        ]);
    }

    /**
     * @Route("/raids", name="app_raids_list")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param RaidRepository $raidRepository
     * @return Response
     */
    public function raidsList(PaginatorInterface $paginator, Request $request, EventsBookRepository $eventsRepository)
    {
        $pagination = $paginator->paginate(
            $eventsRepository->paginationRaidsList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('event/list.html.twig', [
            'events' => $pagination
        ]);
    }

    /**
     * @IsGranted("ROLE_EVENT_MANAGER")
     * @Route("/event/create", name="app_event_create")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createEvent(Request $request)
    {
        $form = $this->createForm(EventFormType::class, new EventsBook(), ['readonly' => false]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            return $this->redirectToRoute('app_event_show', ['slug' => $form->getData()->getSlug()]);
        }

        return $this->render('form/event/createEvent.html.twig', [
            'newEvent' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_EVENT_MANAGER")
     * @Route("/event/edit/{slug}", name="app_event_edit")
     * @param EventsBook $eventsBook
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editEvent(EventsBook $eventsBook, Request $request)
    {
        $form = $this->createForm(EventFormType::class, $eventsBook, ['readonly' => true]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->persist($eventsBook);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_event_show', ['slug' => $eventsBook->getSlug()]);
        }

        return $this->render('form/event/editEvent.html.twig', [
            'editEvent' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_MAP_MANAGER")
     * @Route("/event/delete/{slug}", name="app_event_delete")
     * @param EventsBook $eventsBook
     */
    public function deleteMap(EventsBook $eventsBook)
    {
        try{
            $this->entityManager->remove($eventsBook);
            $this->entityManager->flush();
        }catch (DriverException $e)
        {
            var_dump($e->getMessage());
        }

        return $this->redirectToRoute('app_events_list');
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/event/join/{slug}", name="app_event_join_to")
     */
    public function joinToEvent(EventsBook $eventsBook)
    {
        if($eventsBook->getRegistrationOpeningDate() >= (new \DateTime("now")))
        {
            dd(sprintf("sorry registration to: %s is not allowed", $eventsBook->getName()));
        }

        $repository = $this->getEventRepository($eventsBook->getType());

        if($repository->findOneBy([
            'avatar' => $this->getUser()->getAvatar()->getId(),
            'name' => $eventsBook->getId()
        ]))
        {
            dd(sprintf("sorry you can\'t joint to: %s, you already joined", $eventsBook->getName()));
        }

        $class = $repository->getClassName();

        /** @var Tournament|Raid $event */
        $event = new $class();
        $event->setName($eventsBook);
        $event->setAvatar($this->getUser()->getAvatar());
        $event->setScore(0);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_event_show', ['slug' => $eventsBook->getSlug()]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/event/unsubscribe/{slug}", name="app_event_unsubscribe")
     */
    public function unsubscribeFromEvent(EventsBook $eventsBook)
    {
        if($eventsBook->getRegistrationOpeningDate() >= (new \DateTime("now")))
        {
            dd(sprintf("sorry registration to: %s is not allowed", $eventsBook->getName()));
        }

        $repository = $this->getEventRepository($eventsBook->getType());

        $event = $repository->findOneBy([
            'avatar' => $this->getUser()->getAvatar()->getId(),
            'name' => $eventsBook->getId()
        ]);

        if(!$event)
        {
            dd(sprintf("sorry you must first enroll into: %s", $eventsBook->getName()));
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_event_show', ['slug' => $eventsBook->getSlug()]);
    }

    /**
     * @Route("/event/{slug}", name="app_event_show")
     * @param EventsBook $eventsBook
     */
    public function eventProfile(EventsBook $eventsBook)
    {
        $repository = $this->getEventRepository($eventsBook->getType());

        $members = $repository->findBy(['name' => $eventsBook->getId()]);

        $isEnrolled = false;
        if($this->isGranted("ROLE_USER"))
        {
            $avatarID =  $this->getUser()->getAvatar()->getId();
            $isEnrolled = array_filter($members, function($member) use ($avatarID){ return $member->getAvatar()->getId() === $avatarID; });
        }


        return $this->render('event/profile.html.twig', [
            'event' => $eventsBook,
            'members' => $members,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    private function getEventRepository(string $eventType)
    {
        return $this->entityManager->getRepository($this->getEventType($eventType));
    }

    private function getEventType(string $eventType)
    {
        switch($eventType)
        {
            case 'RAID':
            {
                return Raid::class;
            }

            case 'TOURNAMENT':
            {
                return Tournament::class;
            }
        }
    }
}
