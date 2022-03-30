<?php
declare(strict_types=1);

namespace App\Form\Event;

use App\Entity\Boss;
use App\Entity\EventBoss;
use App\Entity\Map;
use App\Form\DataTransformer\StringToBossTransformer;
use App\Repository\BossRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Exception;

class EventBossFormType extends AbstractType implements DataMapperInterface
{
    public const EVENT_BOSS_FIELDS = [
        'boss' => ['name' => 'boss', 'type' => TextType::class],
        'difficultnessLevel' => ['name' => 'difficultnessLevel', 'type' => IntegerType::class],
        'points' => ['name' => 'points', 'type' => IntegerType::class]
    ];

    /**
     * @var BossRepository
     */
    private BossRepository $bossRepository;

    /**
     * EventBossFormType constructor.
     * @param BossRepository $bossRepository
     */
    public function __construct(BossRepository $bossRepository)
    {
        $this->bossRepository = $bossRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('boss', TextType::class, [
                'attr' => [
                    'readonly' => true,
                ],
                'mapped' => false,
            ])
            ->add('select', ButtonType::class, [
                'attr' => [
                    'type' => 'button',
                    'class' => 'btn btn-secondary btn-block',
                    'id' => 'select_boss',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#selectBossModal'
                ],
            ])
            ->add('create', ButtonType::class, [
                'attr' => [
                    'type' => 'button',
                    'class' => 'btn btn-secondary btn-block',
                    'id' => 'create_boss',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#createBossModal'
                ],
            ])
            ->add('difficultnessLevel', IntegerType::class)
            ->add('points', IntegerType::class)
        ;

        $builder->get('boss')->addModelTransformer(new StringToBossTransformer($this->bossRepository, $options['finder_boss_callback']));

        $builder->setDataMapper($this);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'data_class' => EventBoss::class,
            'finder_boss_callback' => function(BossRepository $bossRepository, string $value): ?Boss {
                return $bossRepository->findOneBy(['id' => $value]);
            },
        ]);
    }

    public function mapDataToForms($viewData, $forms)
    {
        if($viewData === null) {
            return;
        }

        if(!$viewData instanceof EventBoss) {
            throw new Exception\UnexpectedTypeException($viewData, EventBoss::class);
        }

        $forms = iterator_to_array($forms);

        foreach (self::EVENT_BOSS_FIELDS as $name => $value)
        {
            if(array_key_exists($value['name'], $forms)) {

                /** @var string $functionName */
                $functionName = 'get' . ucfirst($value['name']);

                switch($value['type']){

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

        foreach (self::EVENT_BOSS_FIELDS as $name => $value)
        {
            if(array_key_exists($value['name'], $forms)) {

                $functionName = 'set' . ucfirst($value['name']);

                switch($value['type']) {

                    default: {
                        $viewData->$functionName($forms[$value['name']]->getData());

                        break;
                    }
                }
            }
        }
    }
}
