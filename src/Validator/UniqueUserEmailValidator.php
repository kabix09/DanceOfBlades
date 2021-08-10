<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserEmailValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\UniqueUserEmail */

        if (null === $value || '' === $value) {
            return;
        }

        $isEmailExists = $this->userRepository->findOneBy(['email' => $value]);
        if(!$isEmailExists)
        {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }
}
