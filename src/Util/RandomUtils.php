<?php

namespace App\Util;

use Symfony\Component\String\ByteString;
use Symfony\Component\Uid\Uuid;

class RandomUtils
{
    const MAIN_PREFIX = 'DAYN';
    const BUCKET_NUMBERS = "0123456789";

    public static function generateRandomString($length = 10, ?string $bucket = null): string
    {
        // return bin2hex(random_bytes($length));
        return ByteString::fromRandom($length, $bucket)->toString();
    }

    public static function generateWalletPayoutReference(): string
    {
        // return bin2hex(random_bytes($length));
        return self::generateReferenceWithPrefix("PYT");
    }

    public static function generateWalletFundingReference(): string
    {
        // return bin2hex(random_bytes($length));
        return self::generateReferenceWithPrefix("FND");
    }

    public static function generateWalletTransferReference(): string
    {
        // return bin2hex(random_bytes($length));
        return self::generateReferenceWithPrefix("TRF");
    }

    private static function generateReferenceWithPrefix(string $prefix): string
    {
        $date = date('dmY');
        $time = date('His');
        $pattern = $date . '-' . $time;
        $randomness = self::generateRandomString(bucket: self::BUCKET_NUMBERS);
        $prefix = self::MAIN_PREFIX . '-' . $prefix;

        return $prefix . "-" . $randomness . '-' . $pattern;
    }

    public static function generateOtp(): string
    {
        return self::generateRandomString(6, self::BUCKET_NUMBERS);
    }
}