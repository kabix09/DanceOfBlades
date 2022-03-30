<?php
declare(strict_types=1);

namespace App\Form\Event\SelectElement;

use App\Entity\Boss;
use App\Form\DataTransformer\StringToBossTransformer;
use App\Repository\BossRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SelectBossFormType extends AbstractType
{
    /**
     * @var BossRepository
     */
    private BossRepository $bossRepository;

    /**
     * SelectBossFormType constructor.
     * @param BossRepository $bossRepository
     */
    public function __construct(BossRepository $bossRepository)
    {
        $this->bossRepository = $bossRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('select', EntityType::class, [
                'class' => Boss::class,
                'choice_label' => function(Boss $boss) {
                    return $boss->getName();
                },
                'choices' => $this->bossRepository->findAll(),
                'constraints' => [
                    new NotBlank(['message' => 'The boss can\'t be empty'])
                ]
            ])
        ;

        $builder->get('select')->addModelTransformer(new StringToBossTransformer($this->bossRepository, $options['finder_boss_callback']));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'finder_boss_callback' => function(BossRepository $bossRepository, string $value) {
                return $bossRepository->findOneBy(['name' => $value]);
            },
            'readonly' => false
        ]);
    }

}