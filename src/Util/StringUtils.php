<?php

namespace App\Util;

class StringUtils
{
    public static function extractArrayDataFromCsv(string $csv): array
    {
        return array_map('trim', explode(",", $csv));
    }

    public static function convertArrayDataToCsv(array $data): string
    {
        return implode(",", $data);
    }
}