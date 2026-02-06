<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

class WalletSendMoneyRequestDto
{
    #[Assert\NotBlank(message: 'Amount is required.')]
    private int $amount;
    private string $senderUid;
    private string $narration;
}