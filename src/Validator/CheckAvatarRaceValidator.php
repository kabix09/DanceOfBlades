<?php

namespace App\Validator;

use App\Entity\Selection;
use App\Repository\SelectionRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckAvatarRaceValidator extends ConstraintValidator
{
    /**
     * @var SelectionRepository
     */
    private SelectionRepository $selectionRepository;

    public function __construct(SelectionRepository $selectionRepository)
    {

        $this->selectionRepository = $selectionRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint CheckAvatarRace */

        if (null === $value || '' === $value) {
            return;
        }

        if(array_filter($this->selectionRepository->getAvatarRaces(), function (Selection $selection) use ($value){
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
