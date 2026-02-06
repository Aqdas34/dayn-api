<?php

namespace App\Enum;

enum DebtCollectionStatus: string
{
    case PAID = 'paid';
    case PARTIAL = 'partial';
    case UNPAID = 'unpaid';
}