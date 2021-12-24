<?php
declare(strict_types=1);

namespace App\Form\Avatar;

use App\Entity\Selection;
use App\Form\Dto\CreateAvatarModel;
use App\Repository\SelectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateAvatarFormType extends AbstractType
{
    /**
     * @var string
     */
    private string $appCsrfToken;
    /**
     * @var SelectionRepository
     */
    private SelectionRepository $selectionRepository;

    public function __construct(string $appCsrfToken, SelectionRepository $selectionRepository)
    {
        $this->appCsrfToken = $appCsrfToken;
        $this->selectionRepository = $selectionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nick', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Nick can\'t be null']),
                    new Length([
                        'min' =>  6,
                        'minMessage' => 'Your nick must contain at least 8 letters'
                    ])
                ]
            ])
            ->add('race', EntityType::class, [
                'class' => Selection::class,
                'choice_label' => function(Selection $selection) {
                    return sprintf('%s', $selection->getName());
                },
                'placeholder' => 'Choose a race',
                'choices' => $this->selectionRepository->getAvatarRaces(),
                'constraints' => [
                    new NotBlank([
                        'message' => 'You must choose a race'
                    ])
                ]
            ])
            ->add('class', EntityType::class, [
                'class' => Selection::class,
                'choices' => $this->selectionRepository->getAvatarClass(),
                'choice_label' => function(Selection $selection) {
                    return sprintf('%s', $selection->getName());
                },
                'placeholder' => 'Choice a class',
                'constraints' => [
                    new NotBlank([
                        'message' => 'You must choose a class'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return [
            'data_class' => CreateAvatarModel::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf',
            'csrf_token_id' => $this->appCsrfToken
        ];
    }
}