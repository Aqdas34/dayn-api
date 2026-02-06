<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

class WalletTransferRequestDto
{
    #[Assert\NotBlank(message: 'Receiver is required.')]
    private string $receiverUserUid;
    #[Assert\NotBlank(message: 'Amount is required.')]
    private int $amount;

    public function getReceiverUserUid(): string
    {
        return $this->receiverUserUid;
    }

    public function setReceiverUserUid(string $receiverUserUid): WalletTransferRequestDto
    {
        $this->receiverUserUid = $receiverUserUid;
        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): WalletTransferRequestDto
    {
        $this->amount = $amount;
        return $this;
    }
}