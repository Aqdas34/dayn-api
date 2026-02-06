<?php

namespace App\Enum;

enum WalletPayoutStatus: string
{
    case QUEUED = 'queued';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case SUCCESS = 'success';
    case REVERSED = 'reversed';
}
