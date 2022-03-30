<?php

namespace App\Controller;

use App\Entity\Boss;
use App\Entity\EventBoss;
use App\Entity\EventParticipant;
use App\Entity\EventsBook;
use App\Entity\Map;
use App\Form\Boss\BossFormType;
use App\Form\Event\EventBossCollectionFormType;
use App\Form\Event\EventBossFormType;
use App\Form\Event\EventFormType;
use App\Form\Event\EventMapFormType;
use App\Form\Event\SelectElement\SelectBossFormType;
use App\Form\Event\SelectElement\SelectMapFormType;
use App\Repository\EventsBookRepository;
use App\Repository\RaidRepository;
use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
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
     * @Route("/event/create", name="app_create_event")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createEvent(Request $request)
    {
        $event = new EventsBook();
        $event->addBoss(new EventBoss());
        $event->addMap(new Map());

        $form = $this->createForm(EventFormType::class, $event, ['readonly' => false]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->persist($event);
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
            // TODO: if edit type is allowed then after change property it is necessary to remove object from A table and insert into B table associated wth new type
            $this->entityManager->persist($eventsBook);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_event_show', ['slug' => $eventsBook->getSlug()]);
        }

        return $this->render('form/event/editEvent.html.twig', [
            'editEvent' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_EVENT_MANAGER")
     * @Route("/event/delete/{slug}", name="app_event_delete")
     * @param EventsBook $eventsBook
     */
    public function deleteEvent(EventsBook $eventsBook)
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

        $repository = $this->getDoctrine()->getRepository(EventParticipant::class);

        if($repository->findOneBy([
            'event' => $eventsBook,
            'avatar' => $this->getUser()->getAvatar(),
        ]))
        {
            dd(sprintf("sorry you can\'t joint to: %s, you already joined", $eventsBook->getName()));
        }

        $class = $repository->getClassName();

        /** @var EventParticipant $event */
        $event = new $class();
        $event->setEvent($eventsBook);
        $event->setAvatar($this->getUser()->getAvatar());
        $event->setJoinMemberDate(new \DateTime("now"));
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

        $repository = $this->getDoctrine()->getRepository(EventParticipant::class);

        $eventMember = $repository->findOneBy([
            'event' => $eventsBook,
            'avatar' => $this->getUser()->getAvatar()
        ]);

        if(!$eventMember)
        {
            dd(sprintf("sorry you must first enroll into: %s", $eventsBook->getName()));
        }

        $this->entityManager->remove($eventMember);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_event_show', ['slug' => $eventsBook->getSlug()]);
    }

    // MODAL FORM
    // select boss form
    /**
     * @Route("/event/modal-select-boss", name="app_event_form_modal_select_boss")
     */
    public function getSelectBossModalForm(Request $request)
    {
        $form = $this->createForm(SelectBossFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            return $form->getData()['select']['name'];
        }
        return $this->render('form/event/modal/selectBoss.html.twig', [
            'select' => $form->createView()
        ]);
    }

    // select map form
    /**
     * @Route("/event/modal-select-map", name="app_event_form_modal_select_map")
     */
    public function getSelectMapModalForm(Request $request)
    {
        $form = $this->createForm(SelectMapFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            return $form->getData()['select']['name'];
        }
        return $this->render('form/event/modal/selectMap.html.twig', [
            'select' => $form->createView()
        ]);
    }

    // DYNAMIC FORM EVENTS

    // get view of form - boss collection - element -> requested html code by ajax
    /**
     * @Route("/event/boss-colection-card", name="app_event_boss_collection_card")
     * @return Response
     */
    public function getBossColectionCard()
    {
        $form = $this->createForm(EventBossFormType::class, new EventBoss());

        return $this->render('form/event/eventBoss/eventBossCollectionCard.html.twig', [
            'boss' => $form->createView()
        ]);
    }
    /**
     * @Route("/event/map-colection-card", name="app_event_map_collection_card")
     * @return Response
     */
    public function getMapColectionCard()
    {
        $form = $this->createForm(EventMapFormType::class, new Map());

        return $this->render('form/event/eventMap/eventMapCollectionCard.html.twig', [
            'map' => $form->createView()
        ]);
    }

    // EVENT PROFILE
    /**
     * @Route("/event/{slug}", name="app_event_show")
     * @param EventsBook $eventsBook
     */
    public function eventProfile(EventsBook $eventsBook)
    {
        /*
         * Event linked elements:
         * members - $eventsBook->getAvatar()
         * bosses - $eventsBook->getBoss()
         * maps - $eventsBook->getMap()
         */
        $isEnrolled = false;
        if($this->isGranted("ROLE_USER"))
        {
            $avatarID =  $this->getUser()->getAvatar()->getId();
            $isEnrolled = array_filter(iterator_to_array($eventsBook->getAvatar()->getIterator()), function($member) use ($avatarID){ return $member->getAvatar()->getId() === $avatarID; });
        }

        return $this->render('event/profile.html.twig', [
            'event' => $eventsBook,
            'bosses' => $eventsBook->getBoss(),
            'members' => $eventsBook->getAvatar(),
            'maps' => $eventsBook->getMap(),
            'isEnrolled' => $isEnrolled,
        ]);
    }
}
