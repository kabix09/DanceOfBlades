<?php
declare(strict_types=1);

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFormTypeToUploadFileTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if($value === null) {
            return;
        }

        if(!$value instanceof UploadedFile) {
            throw new \LogicException('The CreateAvatarFormType::image can only be used with UploadedFile objects');
        }

        return ['newImage' => $value];  // todo ???
    }

    public function reverseTransform($value)
    {
        if($value === null)
            return;

        if($value['newImage'] instanceof UploadedFile)
            return $value['newImage'];

        throw new \LogicException('The CreateAvatarFormType::image can only be used with UploadedFile objects');
    }
}