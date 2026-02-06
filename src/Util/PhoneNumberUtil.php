<?php

namespace App\Util;

class PhoneNumberUtil
{
    public static function formatNigerianPhoneNumber($phoneNumber): array|string|null
    {
        // Remove all spaces or dashes if they exist
        $phoneNumber = preg_replace('/\s+|-/', '', $phoneNumber);

        // Check if the number starts with 0 and replace it with +234
        if (str_starts_with($phoneNumber, '0')) {
            // Remove the leading 0 and prefix with +234
            $phoneNumber = '+234' . substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }
}