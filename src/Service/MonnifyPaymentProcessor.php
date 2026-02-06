<?php

namespace App\Service;

use App\Enum\DaynTransactionalAccountFeeType;
use App\Model\Integration\Common\PaymentProcessor\ComputePaymentTransactionFeeDto;
use App\Model\Integration\Common\PaymentProcessor\CreateTransactionalAccountRequestDto;
use App\Model\Integration\Common\PaymentProcessor\CreateTransactionalAccountResponseDto;
use App\Model\Integration\Common\PaymentProcessor\VerifyTransactionResponseDto;
use App\Model\Integration\Common\PaymentProvider;
use App\Model\Integration\Monnify\MonnifyCreateReservedAccountRequestDto;
use App\Service\PaymentProcessor\PaymentProcessorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\Value;

#[AsTaggedItem(PaymentProvider::Monnify->value)]
class MonnifyPaymentProcessor implements PaymentProcessorInterface
{
    public function __construct(
        private readonly MonnifyService $monnifyService,
        private readonly LoggerInterface $logger,
        #[Value('%monnify_contract_code%')] private string $contractCode,
    ) {
    }

    public function createTransactionalAccount(CreateTransactionalAccountRequestDto $requestDto): ?CreateTransactionalAccountResponseDto
    {
        $monnifyRequest = (new MonnifyCreateReservedAccountRequestDto())
            ->setAccountReference($requestDto->getTransactionReference())
            ->setAccountName($requestDto->getCustomerName())
            ->setCustomerEmail($requestDto->getCustomerEmail())
            ->setCustomerName($requestDto->getCustomerName())
            ->setContractCode($this->contractCode)
            ->setRestrictPaymentToFixedAmount(true)
            ->setFixedAmount((float)$requestDto->getAmount() * 100); // Convert Naira to Kobo

        $monnifyResponse = $this->monnifyService->createReservedAccount($monnifyRequest);

        if (!$monnifyResponse) {
            $this->logger->error('MonnifyPaymentProcessor: createReservedAccount returned null for reference: ' . $requestDto->getTransactionReference());
            return null;
        }

        // Monnify returns multiple accounts or top level details
        $accounts = $monnifyResponse->getAccounts();
        
        if (!empty($accounts)) {
            $mainAccount = $accounts[0];
            $accountNumber = $mainAccount['accountNumber'];
            $accountName = $mainAccount['accountName'];
            $bankName = $mainAccount['bankName'];
            $bankCode = $mainAccount['bankCode'];
        } elseif ($monnifyResponse->getAccountNumber() !== '') {
            $accountNumber = $monnifyResponse->getAccountNumber();
            $accountName = $monnifyResponse->getAccountName();
            $bankName = $monnifyResponse->getBankName();
            $bankCode = $monnifyResponse->getBankCode();
        } else {
            $this->logger->error('MonnifyPaymentProcessor: No accounts returned in Monnify response for reference: ' . $requestDto->getTransactionReference());
            return null;
        }

        return (new CreateTransactionalAccountResponseDto())
            ->setAccountNumber($accountNumber)
            ->setAccountName($accountName)
            ->setBankName($bankName)
            ->setBankCode($bankCode)
            ->setCurrency('NGN')
            ->setAmount((string)$requestDto->getAmount())
            ->setProviderAccountReference($monnifyResponse->getAccountReference())
            ->setCreatedAt((new \DateTimeImmutable())->format(\DateTimeInterface::ATOM));
    }


    public function computeTransactionFeeFromNairaAmount(int $amount): ComputePaymentTransactionFeeDto
    {
        // 1.5% fee capped at 2000 Naira
        $feeRate = 0.015;
        $transactionFee = $amount * $feeRate;
        
        if ($transactionFee > 2000) {
            $transactionFee = 2000;
        }

        return (new ComputePaymentTransactionFeeDto())
            ->setTransactionFee($transactionFee)
            ->setFeeRate($feeRate * 100)
            ->setFeeType(DaynTransactionalAccountFeeType::Ratio);
    }

    public function verifyTransaction(string $transactionReference): ?VerifyTransactionResponseDto
    {
        // First, try to get transactions for the reserved account (which uses the reference as accountReference)
        $accountTransactions = $this->monnifyService->getReservedAccountTransactions($transactionReference);
        
        if ($accountTransactions && !empty($accountTransactions['content'])) {
            // Find the most recent successful transaction
            foreach ($accountTransactions['content'] as $monnifyData) {
                if ($monnifyData['paymentStatus'] === 'PAID' || $monnifyData['paymentStatus'] === 'OVERPAID') {
                    return (new VerifyTransactionResponseDto())
                        ->setProviderTransactionId($monnifyData['transactionReference'])
                        ->setTransactionReference($monnifyData['paymentReference'])
                        ->setAmountSpecified((float)$monnifyData['amount'] * 100)
                        ->setAmountPaid((float)($monnifyData['amountPaid'] ?? 0) * 100)
                        ->setFee((float)(($monnifyData['payableAmount'] ?? $monnifyData['amount']) - $monnifyData['amount']) * 100)
                        ->setVat(0.0)
                        ->setStampDutyFee(0.0)
                        ->setCurrency($monnifyData['currency'] ?? 'NGN')
                        ->setTransactionStatus(\App\Model\Integration\Common\PaymentProcessor\GenericPaymentProcessorTransactionStatus::SUCCESSFUL)
                        ->setPaymentStartDate($monnifyData['createdOn'])
                        ->setPaymentCompletionDate($monnifyData['completedOn'] ?? null);
                }
            }
        }

        // Fallback to direct transaction verification if no account transactions found or none are successful
        $monnifyData = $this->monnifyService->verifyTransaction($transactionReference);

        if (!$monnifyData) {
            return null;
        }

        $genericStatus = match ($monnifyData['paymentStatus']) {
            'PAID', 'OVERPAID' => \App\Model\Integration\Common\PaymentProcessor\GenericPaymentProcessorTransactionStatus::SUCCESSFUL,
            'PARTIALLY_PAID', 'PENDING' => \App\Model\Integration\Common\PaymentProcessor\GenericPaymentProcessorTransactionStatus::PENDING,
            default => \App\Model\Integration\Common\PaymentProcessor\GenericPaymentProcessorTransactionStatus::FAILED,
        };

        return (new VerifyTransactionResponseDto())
            ->setProviderTransactionId($monnifyData['transactionReference'])
            ->setTransactionReference($monnifyData['paymentReference'])
            ->setAmountSpecified((float)$monnifyData['amount'] * 100)
            ->setAmountPaid((float)($monnifyData['amountPaid'] ?? 0) * 100)
            ->setFee((float)(($monnifyData['payableAmount'] ?? $monnifyData['amount']) - $monnifyData['amount']) * 100)
            ->setVat(0.0)
            ->setStampDutyFee(0.0)
            ->setCurrency($monnifyData['currency'] ?? 'NGN')
            ->setTransactionStatus($genericStatus)
            ->setPaymentStartDate($monnifyData['createdOn'])
            ->setPaymentCompletionDate($monnifyData['completedOn'] ?? null);
    }
}
