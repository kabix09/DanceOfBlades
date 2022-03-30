<?php
declare(strict_types=1);

namespace App\Form\Event\SelectElement;

use App\Entity\Map;
use App\Form\DataTransformer\StringToMapTransformer;
use App\Repository\MapRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SelectMapFormType extends AbstractType
{
    /**
     * @var MapRepository
     */
    private MapRepository $mapRepository;

    /**
     * SelectBossFormType constructor.
     * @param MapRepository $mapRepository
     */
    public function __construct(MapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('select', EntityType::class, [
                'class' => Map::class,
                'choice_label' => function(Map $boss) {
                    return $boss->getName();
                },
                'choices' => $this->mapRepository->findAll(),
                'constraints' => [
                    new NotBlank(['message' => 'The boss can\'t be empty'])
                ]
            ])
        ;

        $builder->get('select')->addModelTransformer(new StringToMapTransformer($this->mapRepository, $options['finder_map_callback']));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'finder_map_callback' => function(MapRepository $mapRepository, string $value) {
                return $mapRepository->findOneBy(['name' => $value]);
            },
            'readonly' => false
        ]);
    }

}