<?php
declare(strict_types=1);

namespace App\Form\Avatar;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ChangeAvatarImageFormType extends AbstractType
{
    /**
     * @var UploadedFile|null
     */
    private ?UploadedFile $uploadedFile;

    public function __construct(?UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newImage', FileType::class, [
                'required' => true,
                'constraints' => [
                    new Image([
                        'maxSize' => ini_get('upload_max_filesize'),
                        'maxSizeMessage' => sprintf('Image musn\'t be bigger than %s', ini_get('upload_max_filesize'))
                    ])
                ],
                'data' => $options['default_value']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        return $resolver->setDefaults([
            'default_value' => $this->uploadedFile
        ]);
    }
}