<?php
declare(strict_types=1);

namespace App\Types;

class MyDateTime extends \DateTime
{
    public function __toString(): string
    {
        return $this->format(MyDateTimeType::MY_DATE_TIME_DEFAULT_FORMAT);
    }
}