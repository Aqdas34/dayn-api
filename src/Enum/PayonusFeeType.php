<?php

namespace App\Enum;

enum PayonusFeeType: string
{
    case RATIO = "Ratio";
    case FIXED = "Fixed";
}