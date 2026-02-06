<?php

namespace App\Model\Integration\Common\PaymentProcessor;

enum GenericPaymentProcessorTransactionStatus: string
{
    case PENDING = 'pending';
    case SUCCESSFUL = 'successful';
    case FAILED = 'failed';
}