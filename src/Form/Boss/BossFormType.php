<?php
declare(strict_types=1);

namespace App\Form\Boss;

use App\Entity\Boss;
use App\Entity\Selection;
use App\Form\DataTransformer\StringToSelectionTransformer;
use App\Repository\SelectionRepository;
use Doctrine\DBAL\Schema\Visitor\RemoveNamespacedAssets;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class BossFormType extends AbstractType
{
    public const BOSS_FIELDS = [
        'name' => ['name' => 'name', 'type' => TextType::class],
        'level' => ['name' => 'level', 'type' => ChoiceType::class]
    ];
    /**
     * @var SelectionRepository
     */
    private SelectionRepository $selectionRepository;

    /**
     * BossFormType constructor.
     * @param SelectionRepository $selectionRepository
     */
    public function __construct(SelectionRepository $selectionRepository)
    {
        $this->selectionRepository = $selectionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                //'attr' => ['readonly' => $options['readonly']]
            ])
            ->add('level', ChoiceType::class, [
                'choices' => [
                    1=>1,
                    2=>2,
                    3=>3,
                    4=>4,
                    5=>5,
                    6=>6,
                    7=>7,
                    8=>8,
                    9=>9,
                    10=>10,
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('strength', IntegerType::class, [
                'constraints' => [
                    new Range(['min' => 1, 'max' => 15])
                ]
            ])
            ->add('defence', IntegerType::class, [
                'constraints' => [
                    new Range(['min' => 1, 'max' => 15])
                ]
            ])
            ->add('health', IntegerType::class, [
                'constraints' => [
                    new Range(['min' => 0, 'max' => 45000])
                ]
            ])
            ->add('magic', IntegerType::class, [
                'constraints' => [
                    new Range(['min' => 0, 'max' => 8000])
                ]
            ])
            ->add('speed', IntegerType::class, [
                'constraints' => [
                    new Range(['min'=> 0, 'max' => 15])
                ]
            ])
            ->add('race', ChoiceType::class, [
//                'mapped' => false,
                'choices' => $this->getChoiceList(),
                'expanded' => true,
            ])
            // make 'race' as checkbox with one option
            // addDataObjectTransformer ( new StringToBossTransformer()) // by name
        ;

        //$builder->get('race')->addModelTransformer(new StringToSelectionTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => Boss::class,
            'readonly' => false,
        ]);
    }

    private function getChoiceList()
    {
        $racesList = [];
        /** @var Selection $race */
        foreach ($this->selectionRepository->getAllRaces() as $race)
        {
            $key =  ucfirst(strtolower($race->getName()));
            $value = $race->getName();

            $racesList[$key] = $value;
        }

        return $racesList;
    }

}