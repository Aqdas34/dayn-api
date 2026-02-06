<?php

namespace App\Enum;

enum DaynPartyType: string
{
    case CREDITOR = 'creditor';
    case DEBTOR = 'debtor';
}