<?php

namespace App\Validator;

use App\Repository\AvatarRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueAvatarNickValidator extends ConstraintValidator
{
    /**
     * @var AvatarRepository
     */
    private AvatarRepository $repository;

    public function __construct(AvatarRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint UniqueAvatarNick */

        if (null === $value || '' === $value) {
            return;
        }

        if(!$this->repository->findOneBy(['nick' => $value]))
        {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
