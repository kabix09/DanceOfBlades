<?php
namespace App\Form\Item;

use App\Entity\Item;
use App\Entity\Selection;
use App\Form\DataTransformer\StringToSelectionTransformer;
use App\Repository\SelectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Exception;

class ItemFormType extends AbstractType implements DataMapperInterface
{
    public const MAP_FIELDS = [
        'name' => ['name' => 'name', 'type' => TextType::class],
        'description' => ['name' => 'description', 'type' => TextareaType::class],
        'level' => ['name' => 'level', 'type' => RangeType::class],
        'type' => ['name' => 'type', 'type' => EntityType::class],
        'group' => ['name' => 'group', 'type' => EntityType::class],
        'value' => ['name' => 'value', 'type' => IntegerType::class],
        'required_user_level' => ['name' => 'required_user_level', 'type' => IntegerType::class],
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
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('level', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 9
                ],
            ])
            ->add('type', EntityType::class, [
                'class' => Selection::class,
                'choices' => $this->selectionRepository->getItemTypes(),
                'choice_label' => function(Selection $selection){
                    return $selection->getName();
                },
                'constraints' => [
                    new NotBlank(['message' => 'The type can\'t be empty'])
                ],
                'attr' => ['readonly' => $options['readonly']]
            ])
            ->add('group', EntityType::class, [
                'class' => Selection::class,
                'choices' => $this->selectionRepository->getItemGroups(),
                'choice_label' => function(Selection $selection){
                    return $selection->getName();
                },
                'constraints' => [
                    new NotBlank(['message' => 'The group can\'t be empty'])
                ],
                'attr' => ['readonly' => $options['readonly']]
            ])
            ->add('value', IntegerType::class)
            ->add('required_user_level', IntegerType::class)
            ->add('image', FileType::class)
        ;

        $builder->get('type')->addModelTransformer(new StringToSelectionTransformer($this->selectionRepository, $options['finder_selection_callback']));
        $builder->get('group')->addModelTransformer(new StringToSelectionTransformer($this->selectionRepository, $options['finder_selection_callback']));

        $builder->setDataMapper($this);

        $builder->get('group')->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Selection $data */
                $data = $event->getData();

                if($data)
                {
                    //set item types dependently by item group

                    $this->setItemTypesList(
                        $event->getForm()->getParent(),
                        $this->selectionRepository->getItemTypesByParent($data->getId())
                    );
                }
            }
        );
    }


    public function mapDataToForms($viewData, $forms)
    {
        if($viewData === null) {
            return;
        }

        if(!$viewData instanceof Item) {
            throw new Exception\UnexpectedTypeException($viewData, Item::class);
        }

        $forms = iterator_to_array($forms);

        foreach (self::MAP_FIELDS as $name => $value)
        {
            if(array_key_exists($value['name'], $forms)) {

                /** @var string $functionName */
                $functionName = 'get' . array_reduce(explode('_', $value['name']), function($result, $actualElement) {
                        return $result . ucfirst($actualElement);
                    });

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

                $functionName = 'set' . array_reduce(explode('_', $value['name']), function($result, $actualElement) {
                    return $result . ucfirst($actualElement);
                });

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


    private function setItemTypesList(FormInterface $form, array $itemTypes)
    {
        $form->add('type', EntityType::class,  [
            'class' => Selection::class,
            'placeholder' => 'Choice a type',
            'choices' => $itemTypes,
            'choice_label' => function(Selection $selection){
                return $selection->getName();
            },
            'constraints' => [
                new NotBlank(['message' => 'Item type can\'t be empty'])
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Item::class,
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
}