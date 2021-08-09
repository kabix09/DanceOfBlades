<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginFormType extends AbstractType
{
    /**
     * @var string
     */
    private $appCsrfToken;

    public function __construct(string $appCsrfToken)
    {
        $this->appCsrfToken = $appCsrfToken;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'This field is required'
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'This email is too short',
                        'max' => 40,
                        'maxMessage' => 'This email is too long'
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'This field is required'
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'This password is too short'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf',   // how to bind value with LoginFormAuthenticator const variable without hard binding classes
            'csrf_token_id' => $this->appCsrfToken
        ]);
    }
}
