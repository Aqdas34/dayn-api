<?php

namespace App\Service\PaymentProcessor;

use App\Model\Integration\Common\PaymentProcessor\ComputePaymentTransactionFeeDto;
use App\Model\Integration\Common\PaymentProcessor\CreateTransactionalAccountRequestDto;
use App\Model\Integration\Common\PaymentProcessor\CreateTransactionalAccountResponseDto;
use App\Model\Integration\Common\PaymentProcessor\VerifyTransactionResponseDto;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface PaymentProcessorInterface
{
    function createTransactionalAccount(CreateTransactionalAccountRequestDto $requestDto): ?CreateTransactionalAccountResponseDto;
    function computeTransactionFeeFromNairaAmount(int $amount): ComputePaymentTransactionFeeDto;
    function verifyTransaction(string $transactionReference): ?VerifyTransactionResponseDto;
}