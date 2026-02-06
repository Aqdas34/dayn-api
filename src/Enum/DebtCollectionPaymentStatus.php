<?php

namespace App\Enum;

enum DebtCollectionPaymentStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
