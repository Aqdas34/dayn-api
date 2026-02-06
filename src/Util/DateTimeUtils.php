<?php

namespace App\Util;

use DateInterval;
use DateTimeImmutable;

class DateTimeUtils
{
    public static function getDateTimeNow(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }

    public static function hasDateTimeElapsed(DateTimeImmutable $dateTime): bool
    {
        $currentDate = new DateTimeImmutable();

        return $currentDate > $dateTime;
    }

    public static function hasDateTimeElapsedInMinutes(DateTimeImmutable $dateTime, int $minutes): bool
    {
        $currentDate = new DateTimeImmutable();
        $dateTime = $dateTime->add(new DateInterval("PT{$minutes}M"));

        return $currentDate > $dateTime;
    }

    public static function createFromString(string $datetime): DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $datetime);
    }
}