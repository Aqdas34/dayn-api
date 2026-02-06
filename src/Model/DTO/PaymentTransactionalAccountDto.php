<?php

namespace App\Model\DTO;

final class PaymentTransactionalAccountDto
{
    private string $reference;
    private string $amountSpecified;
    private string $amountCharged;
    private string $accountNumber;
    private string $accountName;
    private string $bankName;
    private string $bankCode;
    private string $amountToPay;
    private ?\DateTimeImmutable $createdAt;
}