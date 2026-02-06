<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class DebtPayInitiateRequestDto
{
    #[Assert\NotBlank(message: 'Amount is required!')]
    #[Assert\Positive(message: 'Amount must be greater than zero!')]
    private int $amount;

    #[Assert\NotBlank(message: 'Channel is required!')]
    private string $channel;

    #[SerializedName('base_amount')]
    private ?int $baseAmount;


//    #[Assert\NotBlank(message: 'Transaction PIN is required!')]
//    private string $transactionPin;

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): DebtPayInitiateRequestDto
    {
        $this->amount = $amount;
        return $this;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): DebtPayInitiateRequestDto
    {
        $this->channel = $channel;
        return $this;
    }

    public function getBaseAmount(): ?int
    {
        return $this->baseAmount;
    }

    public function setBaseAmount(?int $baseAmount): DebtPayInitiateRequestDto
    {
        $this->baseAmount = $baseAmount;
        return $this;
    }

//    public function getTransactionPin(): string
//    {
//        return $this->transactionPin;
//    }
//
//    public function setTransactionPin(string $transactionPin): void
//    {
//        $this->transactionPin = $transactionPin;
//    }
}