<?php
declare(strict_types=1);

namespace App\Form\DataTransformer;

use App\Entity\Boss;
use App\Repository\BossRepository;
use Symfony\Component\Form\DataTransformerInterface;

class StringToBossTransformer implements DataTransformerInterface
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var BossRepository
     */
    private BossRepository $bossRepository;

    /**
     * StringToSelectionTransformer constructor.
     * @param BossRepository $bossRepository
     * @param callable $callback
     */
    public function __construct(BossRepository $bossRepository, callable $callback)
    {
        $this->callback = $callback;
        $this->bossRepository = $bossRepository;
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
    public function reverseTransform($value): ?Boss
    {
        return ($this->callback)($this->bossRepository, $value) ?? new Boss();
    }
}
