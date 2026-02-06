<?php

namespace App\Model\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class DebtPayCompleteRequestDto
{
    #[Assert\NotBlank(message: 'Payment Identifier is required')]
    #[SerializedName('payment_uid')]
    private string $paymentUid;

    #[Assert\Type(type: 'bool', message: 'Acceptance choice is required')]
    private bool $accepted;

    public function getPaymentUid(): string
    {
        return $this->paymentUid;
    }

    public function setPaymentUid(string $paymentUid): DebtPayCompleteRequestDto
    {
        $this->paymentUid = $paymentUid;
        return $this;
    }

    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): DebtPayCompleteRequestDto
    {
        $this->accepted = $accepted;
        return $this;
    }
}