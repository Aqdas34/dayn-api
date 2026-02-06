<?php

namespace App\Util;

use Symfony\Component\Uid\Uuid;

class UidUtils
{
    public static function generateUid(): string
    {
        return Uuid::v7()->toString();
    }

    public static function generateUidv4(): string
    {
        return Uuid::v4()->toString();
    }
}