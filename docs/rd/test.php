<?php

use JetBrains\PhpStorm\ArrayShape;

#[ArrayShape([
    'amount' => "float",
    'feeRate' => "float",
])]
function getTransactionFee(int $amount) {
    return [
        'amount' => 0.5 * $amount,
        'feeRate' => 0.5,
    ];
}

[$transactionFee, $feeRate] = getTransactionFee(10000);
echo "Amount Charged: " . $transactionFee;
echo PHP_EOL;
echo "Fee Ratio: " . $feeRate;
echo PHP_EOL;