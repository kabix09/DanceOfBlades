<?php
declare(strict_types=1);

namespace App\Form\Map;

use App\Entity\Map;
use App\Entity\Selection;
use App\Form\DataTransformer\StringToSelectionTransformer;
use App\Repository\SelectionRepository;
use Monolog\Formatter\FormatterInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MapFormType extends AbstractType implements DataMapperInterface
{
    public const MAP_FIELDS = [
        'name' => ['name' => 'name', 'type' => TextType::class],
        'description' => ['name' => 'description', 'type' => TextType::class],
        'areaType' => ['name' => 'areaType', 'type' => EntityType::class],
        'terrainType' => ['name' => 'terrainType', 'type' => EntityType::class],
        'isClimateInfluenced' => ['name' => 'isClimateInfluenced', 'type' => CheckboxType::class],
        'climate' => ['name' => 'climate', 'type' => EntityType::class],
        'dangerousLevel' => ['name' => 'dangerousLevel', 'type' => ChoiceType::class],
        'noBattleZone' => ['name' => 'noBattleZone', 'type' => CheckboxType::class],
        'noViolenceZone' => ['name' => 'noViolenceZone', 'type' => CheckboxType::class],
        'noEscapeZone' => ['name' => 'noEscapeZone', 'type' => CheckboxType::class],
        'noMagicZone' => ['name' => 'noMagicZone', 'type' => CheckboxType::class],
        'image' => ['name' => 'image', 'type' => FileType::class]
    ];

    /**
     * @var SelectionRepository
     */
    private SelectionRepository $selectionRepository;
    /**
     * @var string
     */
    private string $appCsrfToken;

    public function __construct(string $appCsrfToken, SelectionRepository $selectionRepository)
    {
        $this->selectionRepository = $selectionRepository;
        $this->appCsrfToken = $appCsrfToken;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Map $map */
        $map = $options['data'] ?? null;

        $builder
            ->add(self::MAP_FIELDS['name']['name'], self::MAP_FIELDS['name']['type'], [
                'constraints' => [
                    new NotBlank(['message' => 'Name can\'t be empty']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Map name must contain at least 6 letters'
                    ])
                ],
                'attr' => ['readonly' => $options['readonly']]
            ])
            ->add(self::MAP_FIELDS['description']['name'], self::MAP_FIELDS['description']['type'])
            ->add(self::MAP_FIELDS['areaType']['name'], self::MAP_FIELDS['areaType']['type'], [
                'class' => Selection::class,
                'placeholder' => 'Choice an area',
                'choices' => $this->selectionRepository->getMapAreas(),
                'choice_label' => function(Selection $selection){
                    return $selection->getName();
                },
                'constraints' => [
                    new NotBlank(['message' => 'Area type can\'t be empty'])
                ]
            ])
            ->add(self::MAP_FIELDS['terrainType']['name'], self::MAP_FIELDS['terrainType']['type'],  [
                'class' => Selection::class,
                'placeholder' => 'Choice a terrain',
                'choices' => $this->selectionRepository->getMapTerrains(),
                'choice_label' => function(Selection $selection){
                    return $selection->getName();
                },
                'constraints' => [
                    new NotBlank(['message' => 'Terrain type can\'t be empty'])
                ]
            ])
            ->add(self::MAP_FIELDS['isClimateInfluenced']['name'], self::MAP_FIELDS['isClimateInfluenced']['type'], [
                'label' => 'climate influence',
                'required' => false
            ])
            ->add(self::MAP_FIELDS['dangerousLevel']['name'], self::MAP_FIELDS['dangerousLevel']['type'], [
                'choices' => [
                    'Normal' => 1,
                    'Experienced' => 2,
                    'Hard' => 3,
                    'Advanced' => 5,
                    'Bloodlust' => 7,
                    'Extremal' => 8,
                    'Death expects fools' => 9,
                    'Run for glory' => 10
                ]
            ])
            ->add(self::MAP_FIELDS['noBattleZone']['name'], self::MAP_FIELDS['noBattleZone']['type'], [
                'label' => 'Are battles forbidden',
                'required' => false
            ])
            ->add(self::MAP_FIELDS['noViolenceZone']['name'], self::MAP_FIELDS['noViolenceZone']['type'], [
                'label' => 'Is violence forbidden',
                'required' => false
            ])
            ->add(self::MAP_FIELDS['noEscapeZone']['name'], self::MAP_FIELDS['noEscapeZone']['type'], [
                'label' => 'Is escape forbidden',
                'required' => false
            ])
            ->add(self::MAP_FIELDS['noMagicZone']['name'], self::MAP_FIELDS['noMagicZone']['type'], [
                'label' => 'Is magic forbidden',
                'required' => false
            ])
            ->add(self::MAP_FIELDS['image']['name'], self::MAP_FIELDS['image']['type'], [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5m',
                        'maxSizeMessage' => 'This file is to large - max size: 5m'
                    ])
                ],
                'label' => 'Select map image'
            ])
        ;

        if($map && !empty($map->getClimate()))
        {
            $builder->add(self::MAP_FIELDS['climate']['name'], EntityType::class, [
                'class' => Selection::class,
                'choices' => $this->selectionRepository->getMapClimates(),
                'choice_label' => function(Selection $selection){
                    return $selection->getName();
                },
                'required' => false
            ]);
        }

        $builder->get('areaType')->addModelTransformer(new StringToSelectionTransformer($this->selectionRepository, $options['finder_selection_callback']));
        $builder->get('terrainType')->addModelTransformer(new StringToSelectionTransformer($this->selectionRepository, $options['finder_selection_callback']));

        $builder->setDataMapper($this);

        $builder->get(self::MAP_FIELDS['isClimateInfluenced']['name'])->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var int $data */
                $data = $event->getData();

                $this->setClimateInfluence($event->getForm()->getParent(), (int)$data);

            }
        );

        $builder->get(self::MAP_FIELDS['areaType']['name'])->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Selection $data */
                $data = $event->getData();

                if($data)
                {
                    //set map terrain dependently by map area

                    $this->setMapTerrainsList(
                        $event->getForm()->getParent(),
                        $this->selectionRepository->getMapTerrainsByParent($data->getId())
                    );
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $formEvent) {

                /** @var Map $data */
                $data = $formEvent->getData();

                $extraData = $formEvent->getForm()->getExtraData();

                if($extraData)
                {
                    $function = 'set' . ucfirst(array_keys($extraData)[0]);
                    $data->$function(
                        $this->selectionRepository->findOneBy(['id' => array_values($extraData)[0]])->getName()
                    );
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Map::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf',
            'csrf_token_id' => $this->appCsrfToken,
            'finder_selection_callback' => function(SelectionRepository $selectionRepository, string $value) {
                return $selectionRepository->findOneBy(['name' => $value]);
            },
            "allow_extra_fields" => true,
            'readonly' => false
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

        foreach (self::MAP_FIELDS as $name => $value)
        {
            if(array_key_exists($value['name'], $forms)) {

                /** @var string $functionName */
                $functionName = 'get' . ucfirst($value['name']);

                switch($value['type']){
                    case EntityType::class: {
                        $forms[$value['name']]->setData(
                            $this->selectionRepository->findOneBy(['name' => $viewData->$functionName()])
                        );
                        break;
                    }

                    case FileType::class: {
                        if($viewData->$functionName())
                        {
                            $forms[$value['name']]->setData(
                                new File($viewData->$functionName())
                            );
                        }

                        break;
                    }


                    default: {
                        $forms[$value['name']]->setData($viewData->$functionName());    // entity data must be access before initialization

                        break;
                    }
                }
            }
        }
    }

    public function mapFormsToData($forms, &$viewData)
    {
        $forms = iterator_to_array($forms);

        foreach (self::MAP_FIELDS as $name => $value)
        {
            if(array_key_exists($value['name'], $forms)) {

                $functionName = 'set' . ucfirst($value['name']);

                switch($value['type']) {
                    case EntityType::class: {

                        // else will be set default entity's field vale
                        if($forms[$value['name']]->getData())
                            $viewData->$functionName($forms[$value['name']]->getData()->getName());

                        break;
                    }

                    case FileType::class: {
                        /** @var UploadedFile $uploadedFile */
                        $uploadedFile = $forms[$value['name']]->getData();
                        // set file original name as entity value
                        $viewData->$functionName($uploadedFile->getClientOriginalName());

                        break;
                    }

                    default: {
                        $viewData->$functionName($forms[$value['name']]->getData());

                        break;
                    }
                }
            }
        }
    }

    private function setClimateInfluence(FormInterface $form, ?int $climate): void
    {
        if($climate === null || $climate === false || $climate === 0){
            $form->remove(self::MAP_FIELDS['climate']['name']);

            return;
        }

        $form->add(self::MAP_FIELDS['climate']['name'], EntityType::class, [
            'class' => Selection::class,
            'placeholder' => 'Choice a climate',
            'choices' => $this->selectionRepository->getMapClimates(),
            'choice_label' => function(Selection $selection){
                return $selection->getName();
            },
        ]);
    }

    private function setMapTerrainsList(FormInterface $form, array $mapTerrains)
    {
        $form->add(self::MAP_FIELDS['terrainType']['name'], self::MAP_FIELDS['terrainType']['type'],  [
            'class' => Selection::class,
            'placeholder' => 'Choice a terrain',
            'choices' => $mapTerrains,
            'choice_label' => function(Selection $selection){
                return $selection->getName();
            },
            'constraints' => [
                new NotBlank(['message' => 'Terrain type can\'t be empty'])
            ]
        ]);
    }
}