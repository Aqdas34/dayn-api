<?php

namespace App\Model\Response;

use Symfony\Component\Serializer\Attribute\SerializedName;

class DebtCollectionStatisticsResponseDto
{
    #[SerializedName('total_creditor_amount')]
    private int $totalCreditorAmount = 0;

    #[SerializedName('total_debtor_amount')]
    private int $totalDebtorAmount = 0;
    private array $unconfirmedDebts = [];

    public function getTotalCreditorAmount(): int
    {
        return $this->totalCreditorAmount;
    }

    public function setTotalCreditorAmount(int $totalCreditorAmount): DebtCollectionStatisticsResponseDto
    {
        $this->totalCreditorAmount = $totalCreditorAmount;
        return $this;
    }

    public function getTotalDebtorAmount(): int
    {
        return $this->totalDebtorAmount;
    }

    public function setTotalDebtorAmount(int $totalDebtorAmount): DebtCollectionStatisticsResponseDto
    {
        $this->totalDebtorAmount = $totalDebtorAmount;
        return $this;
    }

    public function getUnconfirmedDebts(): array
    {
        return $this->unconfirmedDebts;
    }

    public function setUnconfirmedDebts(array $unconfirmedDebts): DebtCollectionStatisticsResponseDto
    {
        $this->unconfirmedDebts = $unconfirmedDebts;
        return $this;
    }
}