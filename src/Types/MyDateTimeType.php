<?php
declare(strict_types=1);

namespace App\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;
use function strlen;

class MyDateTimeType extends DateTimeType
{
    public const MY_DATE_TIME_DEFAULT_FORMAT = 'Y-m-d H:i:s.u';

    private const DATE_TIME_TYPE_NAME = 'mydatetime';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $dateTime = parent::convertToPHPValue($value, $platform);

        if ( ! $dateTime) {
            return $dateTime;
        }
        $newDateTime = new MyDateTime($dateTime->format(self::MY_DATE_TIME_DEFAULT_FORMAT));
        $newDateTime->setTimezone($dateTime->getTimezone());

        return $newDateTime;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $dateTime = parent::convertToDatabaseValue($value, $platform);

//      -!-!- for datetime not for datetime2 -!-!-
//        if(strlen($dateTime) === 26 && $platform->getDateTimeFormatString() == 'Y-m-d H:i:s.u') {
//            $dateTime = substr($dateTime, 0, strlen($dateTime) - 3);
//        }

        return $dateTime;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return self::DATE_TIME_TYPE_NAME;
    }
}
/*
 * solution: https://stackoverflow.com/questions/15080573/doctrine-2-orm-datetime-field-in-identifier
 *
 * TODO: fix this
 * WARNING but this dont work with datetime inserted directly in db or in other ways! - why ???
 * probablu, sql server add 7'th chat in nanosecont but doctrine object fetch only 6 and then sql add 0 to the end, so:  '2021-09-24 17:49:23.7533333' != '2021-09-24 17:49:23.7533330'
 * */