<?php

namespace App\Validator;

use App\Entity\Selection;
use App\Repository\SelectionRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckAvatarClassValidator extends ConstraintValidator
{
    /**
     * @var SelectionRepository
     */
    private SelectionRepository $selectionRepository;

    /**
     * CheckAvatarClassValidator constructor.
     * @param SelectionRepository $selectionRepository
     */
    public function __construct(SelectionRepository $selectionRepository)
    {
        $this->selectionRepository = $selectionRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint CheckAvatarClass */

        if (null === $value || '' === $value) {
            return;
        }

        if(array_filter($this->selectionRepository->getAvatarClass(), function (Selection $selection) use ($value){
            if($value === $selection->getName()) {
                return true;
            }
        }) != [])
        {
            return;
        }


        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
