<?php

namespace App\Service;

use App\Enum\DaynTransactionalAccountFeeType;
use App\Model\Integration\Common\PaymentProcessor\ComputePaymentTransactionFeeDto;
use App\Model\Integration\Common\PaymentProcessor\CreateTransactionalAccountRequestDto;
use App\Model\Integration\Common\PaymentProcessor\CreateTransactionalAccountResponseDto;
use App\Model\Integration\Common\PaymentProcessor\GenericPaymentProcessorTransactionStatus;
use App\Model\Integration\Common\PaymentProcessor\PaymentProviderTransactionDto;
use App\Model\Integration\Common\PaymentProcessor\VerifyTransactionResponseDto;
use App\Model\Integration\Common\PaymentProvider;
use App\Repository\PaymentTransactionalAccountRepository;
use App\Service\PaymentProcessor\PaymentProcessorInterface;
use App\Util\DateTimeUtils;
use App\Util\UidUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(PaymentProvider::Dayn->value)]
class DaynPaymentProcessor implements PaymentProcessorInterface
{
    public function __construct(
        private readonly DaynAccountNumberGenerator $accountNumberGenerator,
        private readonly LoggerInterface $logger,
        private readonly PaymentTransactionalAccountRepository $paymentTransactionalAccountRepository
    )
    {
    }

    public function createTransactionalAccount(CreateTransactionalAccountRequestDto $requestDto): ?CreateTransactionalAccountResponseDto
    {
        try {
            $accountNumber = $this->accountNumberGenerator->generate();
            return (new CreateTransactionalAccountResponseDto())
                ->setAccountNumber($accountNumber)
                ->setAccountName($requestDto->getCustomerName())
                ->setAmount($requestDto->getAmount())
                ->setCurrency("NGN")
                ->setBankCode("010101")
                ->setBankName("Dayn MFB")
                ->setProviderAccountReference("DAYN-$accountNumber")
                ->setCreatedAt(DateTimeUtils::getDateTimeNow()->format("Y-m-d H:i:s"));
        } catch (\InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    function verifyTransaction(string $transactionReference): ?VerifyTransactionResponseDto
    {
        $transaction = $this->paymentTransactionalAccountRepository->findOneBy([
            'transactionReference' => $transactionReference,
        ]);
        if (!$transaction) {
            return null;
        }

        return (new VerifyTransactionResponseDto())
            ->setTransactionReference($transactionReference)
            ->setVat(0)
            ->setFee($this->computeTransactionFeeFromNairaAmount($transaction->getAmountSpecified())->getTransactionFee())
            ->setAmountSpecified((float) $transaction->getAmountSpecified())
            ->setAmountPaid((float) $transaction->getAmountCharged())
            ->setProviderTransactionId(UidUtils::generateUid())
            ->setPaymentStartDate($transaction->getCreatedAt()->format("Y-m-d H:i:s"))
            ->setPaymentCompletionDate($transaction->getCreatedAt()->add(new \DateInterval('PT10M'))->format("Y-m-d H:i:s"))
            ->setCurrency("NGN")
            ->setAppliedStampDutyFee($transaction->getAmountCharged() >= 10000)
            ->setStampDutyFee($transaction->getAmountCharged() >= 10000 ? 50 : 0)
            ->setTransactionStatus(GenericPaymentProcessorTransactionStatus::SUCCESSFUL);
    }


    public function verifyPaymentTransaction(string $transactionReference): ?PaymentProviderTransactionDto
    {
        return (new PaymentProviderTransactionDto());
    }

    public function computeTransactionFeeFromNairaAmount(int $amount): ComputePaymentTransactionFeeDto
    {
        $payonusFee = ($amount) / 100;
        $ourFee = (0.5 * $amount) / 100;
        $totalFee = $payonusFee + $ourFee;
        $feeRate = 1.0 + 0.5;

        return (new ComputePaymentTransactionFeeDto())
            ->setTransactionFee($totalFee)
            ->setFeeRate($feeRate)
            ->setFeeType(DaynTransactionalAccountFeeType::Ratio);
    }
}