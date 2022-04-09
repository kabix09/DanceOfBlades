<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Selection;
use App\Form\Item\ItemFormType;
use App\Repository\ItemRepository;
use App\Service\Uploader;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ItemController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var PaginatorInterface
     */
    private PaginatorInterface $paginator;

    /**
     * MapController constructor.
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     */
    public function __construct(EntityManagerInterface $entityManager, PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->paginator = $paginator;
    }

    /**
     * @IsGranted("ROLE_ITEM_MANAGER")
     * @Route("/item/create", name="app_create_item")
     */
    public function createItem(Request $request, Uploader $uploader): Response
    {
        $form = $this->createForm(ItemFormType::class, new Item(), ['readonly' => false]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Item $newItem */
            $newItem = $form->getData();

            // set slug
            $newItem->setSlug(
                Urlizer::urlize($newItem->getName())
            );

            // upload image
            /** @var UploadedFile $image */
            $image = $form['image']->getData();

            $newItem->setImage(
                Urlizer::urlize(
                    pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '-' . Uuid::uuid4()->toString() . '.' . $image->guessExtension()
            );

            // save entity
            $this->entityManager->persist($newItem);
            $this->entityManager->flush();

            $uploader->uploadItemImage($image, $newItem->getImage());

            return $this->redirectToRoute('app_item_profile', ['slug' => $newItem->getSlug()]);    // change name to slug
        }

        return $this->render('form/item/newItem.html.twig', [
            'newItem' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ITEM_MANAGER")
     * @Route("/item/edit/{slug}", name="app_edit_item")
     * @ParamConverter("resluggle", class="Item", options={"mapping": {"slug": "slug"}})
     * @param Item $item
     * @param Request $request
     * @param Uploader $uploader
     * @param RequestStackContext $requestStackContext
     * @return Response
     */
    public function editItem(Item $item, Request $request, Uploader $uploader, RequestStackContext $requestStackContext): Response
    {
        $item->setImage(
            $requestStackContext->getBasePath() . 'uploads/' .Uploader::ITEM_IMAGE . '/' . $item->getImage()
        );

        $form = $this->createForm(ItemFormType::class,  $item, ['readonly' => false]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $image */
            $image = $form['image']->getData();

            // set slug
            $item->setImage(
                Urlizer::urlize(
                    pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '-' . Uuid::uuid4()->toString() . '.' . $image->guessExtension()
            );

            $this->entityManager->persist($item);
            $this->entityManager->flush();

            $uploader->uploadItemImage($image, $item->getImage());

            return $this->redirectToRoute('app_item_profile', ['slug' => $item->getSlug()]);    // change name to slug
        }

        return $this->render('form/item/editItem.html.twig', [
            'editItem' => $form->createView()
        ]);
    }



    /**
     * @IsGranted("ROLE_ITEM_MANAGER")
     * @Route("/item/remove/{slug}", name="app_delete_item")
     * @ParamConverter("resluggle", class="Item", options={"mapping": {"slug": "slug"}})
     * @param Item $item
     * @return Response
     */
    public function removeItem(Item $item)
    {
        // TODO add bar wit notification

        try{
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }catch (DriverException $e)
        {
            var_dump($e->getMessage());
        }

        return $this->redirectToRoute('app_user_profile');
    }

    /**
     * @Route("/item/item-select", name="app_item_type_select")
     * @param Request $request
     * @return Response
     */
    public function getSpecificTypesDependentOnGroup(Request $request)
    {
        /** @var Selection $selectedArea */
        $selectedGroup = $this->entityManager->getRepository(Selection::class)->findOneBy(['id' => $request->query->get('group')]);
        $item = new Item();
        $item->setGroup($selectedGroup->getName());

        $form = $this->createForm(ItemFormType::class, $item);

        return $this->render('form/item/specificType.html.twig', [
            'newItem' => $form->createView()
        ]);
    }

    // -------- ------ RENDER PAGES ------ --------
    /**
     * @Route("/item/weapon", name="app_items_weapon_list")
     * @param Request $request
     * @return Response
     */
    public function weaponList(Request $request)
    {
        $pagination = $this->paginator->paginate(
            $this->entityManager->getRepository(Item::class)->paginationWeaponList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->renderItemList($pagination);
    }

    /**
     * @Route("/item/outfit", name="app_items_outfit_list")
     * @param Request $request
     * @return Response
     */
    public function outfitList(Request $request)
    {
        $pagination = $this->paginator->paginate(
            $this->entityManager->getRepository(Item::class)->paginationOutfitList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->renderItemList($pagination);
    }

    /**
     * @Route("/item/potion", name="app_items_potion_list")
     * @param Request $request
     * @return Response
     */
    public function potionList(Request $request)
    {
        $pagination = $this->paginator->paginate(
            $this->entityManager->getRepository(Item::class)->paginationMagicItemsList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->renderItemList($pagination);
    }

    /**
     * @Route("/item/grimuar", name="app_items_grimuar_list")
     * @param Request $request
     * @return Response
     */
    public function grimuarList(Request $request)
    {
        // TODO: merge this with function above and change sub menu to be compatible with database items group

        $pagination = $this->paginator->paginate(
            $this->entityManager->getRepository(Item::class)->paginationMagicItemsList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->renderItemList($pagination);
    }

    /**
     * @Route("/item/other", name="app_items_other_list")
     * @param Request $request
     * @return Response
     */
    public function otherList(Request $request)
    {
        $pagination = $this->paginator->paginate(
            $this->entityManager->getRepository(Item::class)->paginationOtherList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->renderItemList($pagination);
    }

    /**
     * @Route("/item/{slug}", name="app_item_profile")
     * @ParamConverter("resluggle", class="Item", options={"mapping": {"slug": "slug"}})
     * @param Item $item
     * @return Response
     */
    public function itemProfile(Item $item){

        return $this->render('item/show.html.twig', [
            'item' => $item
        ]);
    }

    private function renderItemList(PaginationInterface $pagination): Response
    {
        return $this->render('item/list.html.twig', [
            'items' => $pagination
        ]);
    }
}
