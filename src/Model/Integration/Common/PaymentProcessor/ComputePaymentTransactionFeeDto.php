<?php

namespace App\Model\Integration\Common\PaymentProcessor;

use App\Enum\DaynTransactionalAccountFeeType;
use Symfony\Component\Serializer\Attribute\SerializedName;

class ComputePaymentTransactionFeeDto
{
    #[SerializedName('transaction_fee')]
    private float $transactionFee;

    #[SerializedName('fee_rate')]
    private float $feeRate;

    #[SerializedName('fee_type')]
    private DaynTransactionalAccountFeeType $feeType;

    public function getTransactionFee(): float
    {
        return $this->transactionFee;
    }

    public function setTransactionFee(float $transactionFee): ComputePaymentTransactionFeeDto
    {
        $this->transactionFee = $transactionFee;
        return $this;
    }

    public function getFeeRate(): float
    {
        return $this->feeRate;
    }

    public function setFeeRate(float $feeRate): ComputePaymentTransactionFeeDto
    {
        $this->feeRate = $feeRate;
        return $this;
    }

    public function getFeeType(): DaynTransactionalAccountFeeType
    {
        return $this->feeType;
    }

    public function setFeeType(DaynTransactionalAccountFeeType $feeType): ComputePaymentTransactionFeeDto
    {
        $this->feeType = $feeType;
        return $this;
    }
}