<?php
declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Repository\MapRepository;
use App\Repository\SelectionRepository;
use App\Entity\Map;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Sluggable\Util\Urlizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SlugParamConverter implements ParamConverterInterface
{
    private const SLUG_CONVERT_CASES = ['resluggle'];
    /**
     * @var MapRepository
     */
    private MapRepository $mapRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * SlugParamConverter constructor.
     * @param MapRepository $mapRepository
     */
    public function __construct(MapRepository $mapRepository, EntityManagerInterface $entityManager)
    {
        $this->mapRepository = $mapRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $dynamicTypedRepository = $this->entityManager->getRepository('App\Entity\\'.$configuration->getClass());
        $options = $configuration->getOptions()['mapping'];

        if (!isset($options['slug'])) {
            throw new BadRequestHttpException('Slug parameter not provided in request.');
        }

        $matchedEntity = null;

        foreach ($dynamicTypedRepository->getNames() as $index => $content) {
            if(Urlizer::urlize($content['name']) === $request->attributes->get($options['slug']))
            {
                // without this i get 'Unable to guess how to get a Doctrine instance from the request information for parameter "map".' (???)
                $request->attributes->set('name', $content['name']);
                // $request->attributes->remove($options['slug']); // without this attributes have slug => vale & name => name-value
                $matchedEntity = $dynamicTypedRepository->findOneBy(['name' => $content['name']]);

                break;
            }
        }

        $request->attributes->set($configuration->getName(), $matchedEntity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration)
    {

        return
            in_array($configuration->getName(), self::SLUG_CONVERT_CASES, true);
    }
}