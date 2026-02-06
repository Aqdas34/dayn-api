<?php

namespace App\Enum;

enum DebtPaymentChannel: string
{
    case CASH = 'cash';
    case WALLET = 'wallet';
    case PSP = 'psp';
    case PSP_CARD = 'psp_card';
}