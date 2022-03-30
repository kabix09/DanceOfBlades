<?php
declare(strict_types=1);

namespace App\Form\Event;

use App\Entity\Map;
use App\Form\DataTransformer\StringToMapTransformer;
use App\Repository\MapRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Exception;

class EventMapFormType extends AbstractType implements DataMapperInterface
{
    public const EVENT_MAP_FIELDS = [
        'map' => ['name' => 'map', 'type' => TextType::class],
    ];

    /**
     * @var MapRepository
     */
    private MapRepository $mapRepository;

    /**
     * EventBossFormType constructor.
     * @param MapRepository $mapRepository
     */
    public function __construct(MapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('map', TextType::class, [
                'attr' => [
                    'readonly' => true,
                ],
                'mapped' => false,
            ])
            ->add('select', ButtonType::class, [
                'attr' => [
                    'type' => 'button',
                    'class' => 'btn btn-secondary btn-block',
                    'id' => 'select_map',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#selectMapModal'
                ],
            ])
            ->add('create', ButtonType::class, [
                'attr' => [
                    'type' => 'button',
                    'class' => 'btn btn-secondary btn-block',
                    'id' => 'create_map',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#createMapModal'
                ],
            ])
        ;

        $builder->get('map')->addModelTransformer(new StringToMapTransformer($this->mapRepository, $options['finder_map_callback']));
        $builder->setDataMapper($this);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Map::class,
            'finder_map_callback' => function(MapRepository $mapRepository, string $value): ?Map {
                return $mapRepository->findOneBy(['id' => $value]);
            },
        ]);
    }

    public function mapDataToForms($viewData, $forms)
    {
        if($viewData === null) {
            return;
        }

        if(!$viewData instanceof Map) {
            throw new Exception\UnexpectedTypeException($viewData, Map::class);
        }

        $forms = iterator_to_array($forms);

        foreach (self::EVENT_MAP_FIELDS as $name => $value)
        {
            if(array_key_exists($value['name'], $forms)) {

                switch($value['type']){

                    default: {

                        $forms[$value['name']]->setData($viewData);    // entity data must be access before initialization

                        break;
                    }
                }
            }
        }
    }

    public function mapFormsToData($forms, &$viewData)
    {
        $forms = iterator_to_array($forms);

        foreach (self::EVENT_MAP_FIELDS as $name => $value)
        {
            if(array_key_exists($value['name'], $forms)) {

                switch($value['type']) {

                    default: {

                        $viewData = $forms[$value['name']]->getData();

                        break;
                    }
                }
            }
        }
    }
}
