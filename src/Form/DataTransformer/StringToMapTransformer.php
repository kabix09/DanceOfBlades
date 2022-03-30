<?php
declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Map;
use App\Repository\MapRepository;
use Symfony\Component\Form\DataTransformerInterface;

class StringToMapTransformer implements DataTransformerInterface
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var MapRepository
     */
    private MapRepository $mapRepository;

    /**
     * StringToSelectionTransformer constructor.
     * @param MapRepository $mapRepository
     * @param callable $callback
     */
    public function __construct(MapRepository $mapRepository, callable $callback)
    {
        $this->callback = $callback;
        $this->mapRepository = $mapRepository;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        // converts from default value (passed into form data during initialization) to displayed form field value
        if ($value === null)
            return $value;

        return $value->getId() ?? "";
    }

    /**
     * @inheritDoc
     */
    public function reverseTransform($value): ?Map
    {
        return ($this->callback)($this->mapRepository, $value) ?? new Map();
    }
}
