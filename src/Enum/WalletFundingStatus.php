<?php

namespace App\Enum;

enum WalletFundingStatus: string
{
    case PROCESSING = 'processing';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case REVERSED = 'reversed';
}