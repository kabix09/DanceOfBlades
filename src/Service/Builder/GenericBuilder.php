<?php
declare(strict_types=1);

namespace App\Service\Builder;

abstract class GenericBuilder
{
    private $object;

    abstract public function getObject();
}