<?php

namespace App\Controller;

use App\Entity\Boss;
use App\Form\Boss\BossFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BossController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * MapController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @IsGranted("ROLE_BOSS_MANAGER")
     * @Route("/boss/create", name="app_create_boss")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createBoss(Request $request)
    {
        $form = $this->createForm(BossFormType::class, new Boss(), ['readonly' => false]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Boss $newMap */
            $newBoss = $form->getData();

            $this->entityManager->persist($newBoss);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_boss_profile', ['slug' => $newBoss->getSlug()]);    // change name to slug
        }

        return $this->render('form/boss/newBoss.html.twig', [
            'newBoss' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_BOSS_MANAGER")
     * @Route("/boss/edit/{slug}", name="app_edit_boss")
     * @ParamConverter("resluggle", class="Boss", options={"mapping": {"slug": "slug"}})
     * @param Boss $boss
     * @param Request $request
     * @return Response
     */
    public function editBoss(Boss $boss, Request $request)
    {
        $form = $this->createForm(BossFormType::class, $boss, ['readonly' => true]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->entityManager->persist($boss);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_boss_profile', ['slug' => $boss->getSlug()]);    // change name to slug
        }

        return $this->render('form/boss/editBoss.html.twig', [
            'editBoss' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_BOSS_MANAGER")
     * @Route("/boss/delete/{slug}", name="app_delte_boss")
     * @ParamConverter("resluggle", class="Boss", options={"mapping": {"slug": "slug"}})
     * @param Boss $boss
     */
    public function deleteBoss(Boss $boss)
    {
        //TODO do after add event form feature
        // note: what if boss belongst to event - must'n be remove!!!

        // if has children - leave orphaned (check parent - null)
    }

    /**
     * @IsGranted("ROLE_BOSS_MANAGER")
     * @Route("/boss/create-from-data", name="app_create_boss_dynamically")
     * @param Request $request
     * @return Response
     */
    public function createBossByFetch(Request $request)
    {
        $data = $request->query->get('boss_form');

        if($data)
        {
            $boss = new Boss();

            $this->entityManager->persist($boss);

            $boss->setName($data['name']);
            $boss->setLevel($data['level']);
            $boss->setDescription($data['description']);
            $boss->setStrength($data['strength']);
            $boss->setDefence($data['defence']);
            $boss->setHealth($data['health']);
            $boss->setMagic($data['magic']);
            $boss->setSpeed($data['speed']);
            $boss->setRace($data['race']);

            $this->entityManager->flush();

            return new Response(sprintf('%s', $this->entityManager->getRepository(Boss::class)->findOneBy(['slug' => $boss->getSlug()])->getId()));
        }

        return new Response(sprintf('no data was send'));
    }

    // MODAL FORM

    /**
     * @IsGranted("ROLE_BOSS_MANAGER")
     * @Route("/boss/modal-create-boss", name="app_event_form_modal_create_boss")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function getCreateBossModalForm(Request $request)
    {
        $form = $this->createForm(BossFormType::class, new Boss(), ['readonly' => false]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Boss $newBoss */
            $newBoss = $form->getData();

            // save entity
            $this->entityManager->persist($newBoss);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_boss_profile', ['slug' => $newBoss->getSlug()]);    // change name to slug
        }

        return $this->render('form/boss/modal/createBoss.html.twig', [
            'boss' => $form->createView()
        ]);
    }

    /**
     * @Route("/boss/{slug}", name="app_boss_profile")
     * @ParamConverter("resluggle", class="Boss", options={"mapping": {"slug": "slug"}})
     * @param Boss $boss
     * @return Response
     */
    public function bossProfile(Boss $boss){

        return $this->render('boss/show.html.twig', [
            'boss' => $boss
        ]);
    }
}
