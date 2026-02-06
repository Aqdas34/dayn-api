<?php

namespace App\Model\Integration\Common;

enum PaymentProvider: string
{
    case Dayn = 'dayn';
    case Monnify = 'monnify';
}