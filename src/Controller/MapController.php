<?php

namespace App\Controller;

use App\Entity\Map;
use App\Entity\Selection;
use App\Form\Map\MapFormType;
use App\Repository\MapRepository;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Knp\Component\Pager\PaginatorInterface;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MapController
 * @package App\Controller
 */
class MapController extends AbstractController
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
     * @Route("/map", name="app_list_map")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param MapRepository $mapRepository
     * @return Response
     */
    public function mapList(PaginatorInterface $paginator, Request $request, MapRepository $mapRepository)
    {
        $pagination = $paginator->paginate(
            $mapRepository->paginationList(),
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('map/list.html.twig', [
            'maps' => $pagination
        ]);
    }

    /**
     * @IsGranted("ROLE_MAP_MANAGER")
     * @Route("/map/create", name="app_create_map")
     * @param Request $request
     * @param Uploader $uploader
     * @return RedirectResponse|Response
     */
    public function createMap(Request $request, Uploader $uploader)
    {
        $form = $this->createForm(MapFormType::class, new Map(), ['readonly' => false]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var Map $newMap */
            $newMap = $form->getData();

            // set slug
            $newMap->setSlug(
                Urlizer::urlize($newMap->getName())
            );

            // upload image
            /** @var UploadedFile $image */
            $image = $form['image']->getData();

            $newMap->setImage(
                Urlizer::urlize(
                    pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '-' . Uuid::uuid4()->toString() . '.' . $image->guessExtension()
            );

            // save entity
            $this->entityManager->persist($newMap);
            $this->entityManager->flush();

            $uploader->uploadMapImage($image, $newMap->getImage());

            return $this->redirectToRoute('app_map_profile', ['slug' => $newMap->getSlug()]);    // change name to slug
        }

        return $this->render('form/map/newMap.html.twig', [
            'newMap' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_MAP_MANAGER")
     * @Route("/map/edit/{slug}", name="app_edit_map")
     * @ParamConverter("resluggle", class="Map", options={"mapping": {"slug": "slug"}})
     * @param Map $map
     * @param Request $request
     * @param Uploader $uploader
     * @param RequestStackContext $requestStackContext
     * @return Response
     */
    public function editMap(Map $map, Request $request, Uploader $uploader, RequestStackContext $requestStackContext)
    {
        $map->setImage(
            $requestStackContext->getBasePath() . 'uploads/' .Uploader::MAP_IMAGE . '/' . $map->getImage()
        );

        $form = $this->createForm(MapFormType::class, $map, ['readonly' => true]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $image */
            $image = $form['image']->getData();

            $map->setImage(
                Urlizer::urlize(
                    pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '-' . Uuid::uuid4()->toString() . '.' . $image->guessExtension()
            );

            $this->entityManager->persist($map);
            $this->entityManager->flush();

            $uploader->uploadMapImage($image, $map->getImage());

            return $this->redirectToRoute('app_map_profile', ['slug' => $map->getSlug()]);    // change name to slug
        }

        return $this->render('form/map/editMap.html.twig', [
            'editMap' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_MAP_MANAGER")
     * @Route("/map/delete/{slug}", name="app_delte_map")
     * @ParamConverter("resluggle", class="Map", options={"mapping": {"slug": "slug"}})
     * @param Map $map
     */
    public function deleteMap(Map $map)
    {
        $this->entityManager->remove($map);
        $this->entityManager->flush();


        // delete map
        // if has children - leave orphaned (check parent - null)
    }

    /**
     * @Route("/map/terrain-select", name="app_map_terrain_select")
     * @param Request $request
     * @return Response
     */
    public function getSpecificTerrainDependentOnArea(Request $request)
    {
        /** @var Selection $selectedArea */
        $selectedArea = $this->entityManager->getRepository(Selection::class)->findOneBy(['id' => $request->query->get('area')]);
        $map = new Map();
        $map->setAreaType($selectedArea->getName());

        $form = $this->createForm(MapFormType::class, $map);

        return $this->render('form/map/specificTerrain.html.twig', [
            'newMap' => $form->createView()
        ]);
    }

    /**
     * @Route("/map/climate-influence-add", name="app_map_climate_influence_add")
     * @param Request $request
     * @return Response
     */
    public function addClimateInfluence(Request $request)
    {
        if($request->query->get('climateInfluence') !== "true") {
            return new Response(null, 204);
        }

        $newMap = new Map();
        $newMap->setIsClimateInfluenced(true);

        $form = $this->createForm(MapFormType::class, $newMap);

        // dont show isClimateInfluenced
        return $this->render('form/map/climateInfluence.html.twig', [
            'newMap' => $form->createView()
        ]);
    }

    /**
     * @Route("/map/{slug}", name="app_map_profile")
     * @ParamConverter("resluggle", class="Map", options={"mapping": {"slug": "slug"}})
     * @param Map $map
     * @return Response
     */
    public function mapProfile(Map $map){

        return $this->render('map/show.html.twig', [
            'map' => $map
        ]);
    }
}

