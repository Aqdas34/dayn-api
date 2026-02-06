<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PaymentTransactionalAccount;
use App\Entity\User;
use App\Entity\WalletFunding;
use App\Entity\WalletPayout;
use App\Enum\WalletFundingStatus;
use App\Enum\WalletPayoutStatus;
use App\Event\WalletPayoutInitiatedEvent;
use App\Exception\Api\BadRequestException;
use App\Exception\Api\InternalServerException;
use App\Exception\Api\NotFoundException;
use App\Model\DTO\WalletFundingDto;
use App\Model\DTO\WalletPayoutDto;
use App\Model\Integration\Common\PaymentProcessor\CreateTransactionalAccountRequestDto;
use App\Model\Integration\Common\PaymentProvider;
use App\Model\Integration\Common\PaymentTransactionChannel;
use App\Model\Integration\Payonus\PayonusCreateTransactionAccountRequestDto;
use App\Model\Request\InitiateWalletFundingRequestDto;
use App\Model\Request\InitiateWalletFundingResponseDto;
use App\Model\Request\WalletDemoFundRequestDto;
use App\Model\Request\WalletInitiatePayoutRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Repository\PaymentTransactionalAccountRepository;
use App\Repository\UserBankAccountRepository;
use App\Repository\WalletFundingRepository;
use App\Repository\WalletPayoutRepository;
use App\Service\AppLoggerService;
use App\Service\PaymentProcessor\PaymentProcessorProvider;
use App\Service\MonnifyService;
use App\Service\WalletFundingService;
use App\Util\DateTimeUtils;
use App\Util\MoneyUtil;
use App\Util\RandomUtils;
use App\Util\UidUtils;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/wallet', name: 'api_wallet_')]
class WalletController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface                $entityManager,
        private readonly UserBankAccountRepository             $bankAccountRepository,
        private readonly EventDispatcherInterface              $eventDispatcher,
        private readonly WalletPayoutRepository                $walletPayoutRepository,
        private readonly LoggerInterface                       $logger,
        private readonly SerializerInterface                   $serializer,
        private readonly WalletFundingRepository               $walletFundingRepository,
        private readonly PaymentTransactionalAccountRepository $paymentTransactionalAccountRepository, private readonly WalletFundingService $walletFundingService,
    )
    {
    }

    #[Route('/info', name: 'info', methods: ['GET'])]
    public function info(
        #[CurrentUser] User $user
    ): JsonResponse
    {
        $wallet = $user->getWallet();

        $response = new ApiDataResponse([
            'balance' => $wallet->getBalance(),
        ]);

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/fund', name: 'fund', methods: ['POST'])]
    public function fundWallet(
        #[MapRequestPayload] WalletDemoFundRequestDto $requestDto,
        #[CurrentUser] User $user
    ): JsonResponse
    {
        $wallet = $user->getWallet();
        
        // LOCKING: Prevent concurrent balance updates
        $this->entityManager->lock($wallet, LockMode::PESSIMISTIC_WRITE);
        $this->entityManager->refresh($wallet);

        $prevBalance = $wallet->getBalance();
        $amountToAdd = MoneyUtil::toKobo($requestDto->getAmount());
        $newBalance = MoneyUtil::add($prevBalance, $amountToAdd);
        $wallet->setBalance((string)$newBalance);

        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        $response = new ApiDataResponse([
            'prev_balance' => MoneyUtil::toNaira((int)$prevBalance),
            'new_balance' => MoneyUtil::toNaira((int)$wallet->getBalance()),
        ]);

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/send-money', name: 'send-money', methods: ['POST'])]
    public function initiateWalletPayout(
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] WalletInitiatePayoutRequestDto $requestDto,
        MonnifyService $monnifyService
    ): JsonResponse
    {
        $wallet = $currentUser->getWallet();
        
        // LOCKING: Prevent concurrent balance updates
        $this->entityManager->lock($wallet, LockMode::PESSIMISTIC_WRITE);
        $this->entityManager->refresh($wallet);

        $prevBalance = $wallet->getBalance();
        $amount = MoneyUtil::toKobo($requestDto->getAmount());
        
        $transferFee = $monnifyService->computeTransferFeeFromNairaAmount((float)$requestDto->getAmount());
        $transferFeeKobo = MoneyUtil::toKobo($transferFee);

        $totalDebit = MoneyUtil::add($amount, $transferFeeKobo);

        if ($totalDebit > (int)$prevBalance) {
            throw new BadRequestException(sprintf(
                "Your current balance: %s is less than payout amount: %s + fee: %s.",
                MoneyUtil::toNaira((int)$prevBalance),
                MoneyUtil::toNaira($amount),
                MoneyUtil::toNaira($transferFeeKobo)
            ));
        }
        
        $userBankAccount = $this->bankAccountRepository->findOneBy([
            'user' => $currentUser,
            'id' => $requestDto->getUserBankAccountId(),
        ]);
        if (!$userBankAccount) {
            throw new BadRequestException("User bank account not found.");
        }
        $walletPayout = (new WalletPayout())
            ->setReference(UidUtils::generateUid())
            ->setAmount((string) $amount)
            ->setTransactionFee((string) $transferFeeKobo)
            ->setNarration($requestDto->getNarration())
            ->setStatus(WalletPayoutStatus::QUEUED)
            ->setStatusMessage("Queued For Processing")
            ->setWallet($wallet)
            ->setReceivingBankAccount($userBankAccount);
        $this->entityManager->persist($walletPayout);

        // Update Wallet Balance
        $newBalance = MoneyUtil::subtract($wallet->getBalance(), $totalDebit);
        $wallet->setBalance((string)$newBalance);
        $this->entityManager->persist($wallet);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new WalletPayoutInitiatedEvent($wallet->getId()));

        $response = new ApiDataResponse([
            'reference' => $walletPayout->getReference(),
        ]);

        return $this->json($response, Response::HTTP_CREATED);
    }

    #[Route('/payouts/list', name: 'payouts_list', methods: ['GET'])]
    public function listWalletPayouts(
        #[CurrentUser] User $user,
    ): JsonResponse
    {
        $wallet = $user->getWallet();
        $walletPayouts = $wallet->getWalletPayouts()
            ->map(fn (WalletPayout $walletPayout) => WalletPayoutDto::fromWalletPayout($walletPayout))
            ->toArray();

        $apiResponse = new ApiDataResponse($walletPayouts);

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/payouts/find/uid/{uid}', name: 'payouts_find_by_uid', methods: ['GET'])]
    public function findWalletPayout(
        string $uid,
        #[CurrentUser] User $user,
        AppLoggerService $appLoggerService
    ): JsonResponse
    {
        $walletPayout = $this->walletPayoutRepository->findOneBy([
            'uid' => $uid,
        ]);
        if (!$walletPayout || $walletPayout->getWallet()->getUser() !== $user) {
            throw new NotFoundException("The specified payout was not found!");
        }

        $walletPayoutDto = WalletPayoutDto::fromWalletPayout($walletPayout);

        // $this->logger->info($walletPayout->getGatewayResponseObject());
        $appLoggerService->logObjectAsJson($walletPayout->getGatewayResponseObject());

        $apiResponse = new ApiDataResponse($walletPayoutDto, "Wallet Payout retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/funding/list', name: 'funding_list', methods: ['GET'])]
    public function listWalletFundings(
        #[CurrentUser] User $user,
    ): JsonResponse
    {
        $wallet = $user->getWallet();
        $walletFundings = $this->walletFundingRepository->findBy([
            'wallet' => $wallet,
        ], ['createdAt' => 'DESC']);
        $walletFundings = (new ArrayCollection($walletFundings))
            ->map(fn (WalletFunding $walletFunding) => WalletFundingDto::fromWalletFunding($walletFunding))
            ->toArray();

        $apiResponse = new ApiDataResponse($walletFundings);

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/funding/initiate', name: 'funding_initiate', methods: ['POST'])]
    public function initiateWalletFunding(
        #[CurrentUser] User                                  $currentUser,
        #[MapRequestPayload] InitiateWalletFundingRequestDto $requestDto,
        PaymentProcessorProvider $paymentProcessorProvider
    ): JsonResponse
    {
        try {
            $paymentProvider = PaymentProvider::Monnify;
            $paymentProcessor = $paymentProcessorProvider->getProcessor($paymentProvider);
            if (!$paymentProcessor) {
                throw new InternalServerException("Payment Service Provider currently not available!");
            }

            $totalAmount = MoneyUtil::toKobo($requestDto->amount); // Convert Naira to Kobo
            $feeData = $paymentProcessor->computeTransactionFeeFromNairaAmount((int)$requestDto->amount);
            $transactionFee = (int)($feeData->getTransactionFee() * 100); // Convert Naira to Kobo
            $feeRate = $feeData->getFeeRate();
            $feeType = $feeData->getFeeType();
            $totalTransactionFee = $transactionFee;
            $totalAmountWithFee = $totalAmount + $totalTransactionFee;
            $wallet = $currentUser->getWallet();
        $walletFundingReference = RandomUtils::generateWalletFundingReference();
        $walletFunding = (new WalletFunding())
            ->setUid(UidUtils::generateUid())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setLastModifiedAt(new \DateTimeImmutable())
            ->setReference($walletFundingReference)
            ->setTransactionChannel(PaymentTransactionChannel::BANK_TRANSFER)
            ->setAmount((string)$totalAmount) // Add collection fee
            ->setTransactionFee((string)$totalTransactionFee)
            ->setWallet($wallet)
            ->setStatus(WalletFundingStatus::PROCESSING)
            ->setStatusMessage("Transaction in Processing")
            ->setNarration("Wallet Funding for $walletFundingReference");
        $this->entityManager->persist($walletFunding);

        // Generate a unique email alias to bypass "1 account per customer" limit in Monnify
        // Format: original+timestamp@domain (e.g. user+1700000000@gmail.com)
        // This treats each funding session as a "new" customer allocation while keeping email delivery functional.
        $email = $currentUser->getUsername();
        $emailParts = explode('@', $email);
        $localPart = explode('+', $emailParts[0])[0]; // Strip existing aliases
        $aliasSuffix = '+' . time();
        $uniqueMonnifyEmail = $localPart . $aliasSuffix . '@' . $emailParts[1];

        $virtualAccountRequest = (new CreateTransactionalAccountRequestDto())
            ->setTransactionReference($walletFundingReference)
            ->setAmount((float)$totalAmountWithFee / 100) // Convert Kobo back to Naira for Processor
            ->setCustomerName($currentUser->getFullName())
            ->setCustomerEmail($uniqueMonnifyEmail);

        $gatewayResponse = $paymentProcessor->createTransactionalAccount($virtualAccountRequest);
        if (!$gatewayResponse) {
            throw new InternalServerException("An error occurred while attempting to create virtual account!");
        }

        $transactionalAccount = (new PaymentTransactionalAccount())
            ->setUid(UidUtils::generateUid())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setLastModifiedAt(new \DateTimeImmutable())
            ->setAccountNumber($gatewayResponse->getAccountNumber())
            ->setAccountName($gatewayResponse->getAccountName() ?? "Dayn Virtual Account")
            ->setBankName($gatewayResponse->getBankName())
            ->setBankCode($gatewayResponse->getBankCode())
            ->setCustomerName($gatewayResponse->getAccountName() ?? $currentUser->getFullName())
            ->setAmountSpecified((string)$totalAmount)
            ->setAmountCharged((string)$totalAmountWithFee)
            ->setFee((string)$totalTransactionFee)
            ->setFeeRate((string)$feeRate)
            ->setFeeType($feeType)
            ->setProvider($paymentProvider)
            ->setProviderReference($gatewayResponse->getProviderAccountReference())
            ->setProviderResponseObject($this->serializer->normalize($gatewayResponse))
            ->setTransactionReference($walletFundingReference);
        $this->entityManager->persist($transactionalAccount);

            $this->entityManager->flush();

            // $responseData = WalletFundingDto::fromWalletFunding($walletFunding);
            $responseData = (new InitiateWalletFundingResponseDto())
                ->setReference($walletFundingReference)
                ->setAmount($transactionalAccount->getAmountSpecified())
                ->setAmountToPay($transactionalAccount->getAmountCharged())
                ->setAccountName($transactionalAccount->getAccountName())
                ->setAccountNumber($transactionalAccount->getAccountNumber())
                ->setBankName($transactionalAccount->getBankName())
                ->setBankCode($transactionalAccount->getBankCode())
                ->setCreatedAt($transactionalAccount->getCreatedAt());
            $apiResponse = new ApiDataResponse($responseData);

            return $this->json($apiResponse, Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            $this->logger->error('Wallet Funding Error: ' . $e->getMessage(), [
                'exception' => $e,
                'user' => $currentUser->getUserIdentifier(),
                'reference' => $walletFundingReference ?? 'N/A'
            ]);
            throw $e;
        }
    }

    #[Route('/funding/verify/{reference}', name: 'funding_verify', methods: ['GET'])]
    public function verifyWalletFunding(string $reference, #[CurrentUser] User $currentUser): Response
    {
        try {
            $walletFunding = $this->walletFundingRepository->findOneBy(['reference' => $reference]);
            if (!$walletFunding) {
                throw new NotFoundException("Transaction with reference: $reference not found!");
            }

            if ($walletFunding->getTransactionChannel() !== PaymentTransactionChannel::BANK_TRANSFER) {
                throw new BadRequestException("Can not confirm transaction status at this time!");
            }

            $this->walletFundingService->processProcessingWalletFunding($walletFunding);

            $walletFunding = $this->walletFundingRepository->findOneBy([
                'reference' => $reference
            ]);
            if (!$walletFunding) {
                throw new NotFoundException("Transaction with reference: $reference not found!");
            }

            $responseDto = new ApiDataResponse(WalletFundingDto::fromWalletFunding($walletFunding));
            return $this->json($responseDto, Response::HTTP_OK);
        } catch (\Throwable $e) {
            $this->logger->error('Wallet Funding Verification Error: ' . $e->getMessage(), [
                'exception' => $e,
                'reference' => $reference,
                'user' => $currentUser->getUserIdentifier()
            ]);
            throw $e;
        }
    }

    #[Route('/funding/account-details/{reference}', name: 'funding_account_details', methods: ['GET'])]
    public function fetchVirtualAccountDetails(string $reference, #[CurrentUser] User $currentUser): Response
    {
        $walletFunding = $this->walletFundingRepository->findOneBy(['reference' => $reference]);
        if (!$walletFunding) {
            throw new NotFoundException("Transaction with reference: $reference not found!");
        }

        $paymentTransactionalAccount = $this->paymentTransactionalAccountRepository->findOneBy(['transactionReference' => $reference]);
        if (!$paymentTransactionalAccount) {
            throw new NotFoundException("Transaction with reference: $reference not found!");
        }

        $responseDto = new ApiDataResponse(InitiateWalletFundingResponseDto::fromPaymentTransactionalAccount($paymentTransactionalAccount));
        return $this->json($responseDto, Response::HTTP_OK);
    }
}
