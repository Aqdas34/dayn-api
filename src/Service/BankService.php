<?php

namespace App\Service;

use App\Model\Integration\Common\AccountValidationResponseDto;
use App\Model\Integration\Common\BankDto;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

class BankService
{
    private const CACHE_KEY = 'banks';

    public function __construct(
        private readonly MonnifyService $monnifyService,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * @return array|BankDto[]
     */
    public function listBanks(): array
    {
        try {
            return $this->cache->get(self::CACHE_KEY, function (CacheItem $cacheItem) {
                $monnifyBanks = $this->monnifyService->getBankList() ?? [];
                $banks = [];
                foreach ($monnifyBanks as $monnifyBank) {
                    $banks[] = (new BankDto())
                        ->setName($monnifyBank['name'])
                        ->setCode($monnifyBank['code']);
                }
                return $banks;
            });
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage());
        }

        return [];
    }

    public function findBankByCode(string $bankCode): ?BankDto
    {
        $banks = $this->listBanks();
        foreach ($banks as $bank) {
            if ($bank->getCode() === $bankCode) {
                return $bank;
            }
        }

        return null;
    }

    public function validateBankAccount(string $accountNumber, string $bankCode): ?AccountValidationResponseDto
    {
        $monnifyResponse = $this->monnifyService->validateBankAccount($accountNumber, $bankCode);
        if (!$monnifyResponse) {
            return null;
        }

        return (new AccountValidationResponseDto())
            ->setAccountNumber($monnifyResponse['accountNumber'])
            ->setAccountName($monnifyResponse['accountName'])
            ->setBankCode($bankCode);
    }
}