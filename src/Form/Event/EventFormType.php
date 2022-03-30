<?php
declare(strict_types=1);

namespace App\Form\Event;

use App\Entity\Boss;
use App\Entity\EventBoss;
use App\Entity\EventsBook;
use App\Entity\Selection;
use App\Form\DataTransformer\StringToSelectionTransformer;
use App\Repository\SelectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventFormType extends AbstractType implements DataMapperInterface
{
    public const EVENT_FIELDS = [
        'name' => ['name' => 'name', 'type' => TextType::class],
        'description' => ['name' => 'description', 'type' => TextareaType::class],
        'level' => ['name' => 'level', 'type' => RangeType::class],
        'registrationOpeningDate' => ['name' => 'registrationOpeningDate', 'type' => DateTimeType::class],
        'startEventDate' => ['name' => 'startEventDate', 'type' => DateTimeType::class],
        'endEventDate' => ['name' => 'endEventDate', 'type' => DateTimeType::class],
        'type' => ['name' => 'type', 'type' => EntityType::class],
        'boss' => ['name' => 'boss', 'type' => CollectionType::class],
    ];

    /**
     * @var SelectionRepository
     */
    private SelectionRepository $selectionRepository;

    /**
     * EventFormType constructor.
     * @param SelectionRepository $selectionRepository
     */
    public function __construct(SelectionRepository $selectionRepository)
    {
        $this->selectionRepository = $selectionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('level', RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 15
                ],
            ])
            ->add('registrationOpeningDate', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label' => 'Registration date'
            ])
            ->add('startEventDate', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label' => 'Event start date'
            ])
            ->add('endEventDate', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label' => 'End of event date'
            ])
            ->add('type', EntityType::class, [
                'class' => Selection::class,
                'choices' => $this->selectionRepository->getEventsTypes(),
                'choice_label' => function(Selection $selection){
                    return $selection->getName();
                },
                'constraints' => [
                    new NotBlank(['message' => 'The type can\'t be empty'])
                ],
                'attr' => ['readonly' => $options['readonly']]
            ])
            // add boss
            ->add('boss', CollectionType::class, [
                'entry_type' => EventBossFormType::class,
                'entry_options' => [
                    'label' => false
                ],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'data' => $options['data']->getBoss(),
                'prototype' => true,
            ])
        ;

        $builder->setDataMapper($this);
        $builder->get('type')->addModelTransformer(new StringToSelectionTransformer($this->selectionRepository, $options['finder_selection_callback']));
    }


    public function mapDataToForms($viewData, $forms)
    {
        if($viewData === null) {
            return;
        }

        if(!$viewData instanceof EventsBook) {
            throw new Exception\UnexpectedTypeException($viewData, EventsBook::class);
        }

        $forms = iterator_to_array($forms);

        foreach (self::EVENT_FIELDS as $name => $value)
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

                    case RangeType::class: {
                        $forms[$value['name']]->setData((int)$viewData->$functionName());

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

        foreach (self::EVENT_FIELDS as $name => $value)
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

                    case RangeType::class: {
                        $viewData->$functionName((int)($forms[$value['name']]->getData()));

                        break;
                    }

                    // TODO: REMEMBR Assign collection child to parent object - symfony don't map this defaults
                    case CollectionType::class: {
                        $collectionFunction = 'add' . ucfirst($value['name']);

                        foreach ($forms[$value['name']]->getData() as $object)
                        {
                            $object->setEvent($viewData);
                            $viewData->$collectionFunction($object);
                        }

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

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => EventsBook::class,
            'finder_selection_callback' => function(SelectionRepository $selectionRepository, string $value) {
                return $selectionRepository->findOneBy(['name' => $value]);
            },
            'readonly' => false
        ]);
    }
}