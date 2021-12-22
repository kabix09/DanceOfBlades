<?php
declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Selection;
use App\Repository\SelectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class StringToSelectionTransformer implements DataTransformerInterface
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var SelectionRepository
     */
    private SelectionRepository $selectionRepository;

    /**
     * StringToSelectionTransformer constructor.
     * @param SelectionRepository $selectionRepository
     * @param callable $callback
     */
    public function __construct(SelectionRepository $selectionRepository, callable $callback)
    {
        $this->callback = $callback;
        $this->selectionRepository = $selectionRepository;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        // change string for object expected by form element

        if($value === null)
            return $value;

        $callback = $this->callback;
        return $callback($this->selectionRepository, $value);
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value)
    {
        // pass string value from form without changing to mapped object
        return $value;
    }
}