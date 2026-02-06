<?php

namespace App\Model\Integration\Common;

enum PaymentTransactionChannel: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case CHECKOUT = 'checkout';
}