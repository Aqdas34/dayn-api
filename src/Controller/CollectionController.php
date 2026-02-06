<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\DebtCollection;
use App\Entity\DebtCollectionPayment;
use App\Entity\PaymentTransactionalAccount;
use App\Entity\User;
use App\Entity\WalletTransfer;
use App\Enum\DaynPartyType;
use App\Enum\DebtCollectionConfirmationStatus;
use App\Enum\DebtCollectionPaymentStatus;
use App\Enum\DebtCollectionStatus;
use App\Enum\DebtPaymentChannel;
use App\Event\NewDebtCollectionEvent;
use App\Exception\Api\BadRequestException;
use App\Exception\Api\NotFoundException;
use App\Model\DTO\DebtCollectionDto;
use App\Model\DTO\DebtCollectionPaymentDto;
use App\Model\Request\AddDebtRequestDto;
use App\Model\Request\ConfirmDebtRequestDto;
use App\Model\DTO\InitiatePspPaymentResponseDto;
use App\Model\Integration\Common\PaymentProcessor\CreateTransactionalAccountRequestDto;
use App\Model\Integration\Common\PaymentProvider;
use App\Model\Request\DebtPayCompleteRequestDto;
use App\Model\Request\DebtPayInitiateRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Model\Response\DebtCollectionStatisticsResponseDto;
use App\Repository\DebtCollectionPaymentRepository;
use App\Repository\DebtCollectionRepository;
use App\Repository\UserRepository;
use App\Repository\WitnessBindingRepository;
use App\Service\PaymentProcessor\PaymentProcessorProvider;
use App\Service\SmsService;
use Symfony\Component\Serializer\SerializerInterface;
use App\Util\DateTimeUtils;
use App\Util\HashingUtil;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/debt-collection', name: 'api_collection_')]
class CollectionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly SmsService $smsService,
        private readonly LoggerInterface $logger,
        private readonly DebtCollectionRepository $debtCollectionRepository,
        private readonly DebtCollectionPaymentRepository $debtCollectionPaymentRepository,
        private readonly HashingUtil $hashingUtil,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly PaymentProcessorProvider $paymentProcessorProvider,
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route('/statistics', name: 'statistics', methods: ['GET'])]
    public function statistics(
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $totalCreditorAmount = $this->debtCollectionRepository->sumAmountUnpaidByCreditor($currentUser);
        $totalDebtorAmount = $this->debtCollectionRepository->sumAmountUnpaidByDebtor($currentUser);

        $unconfirmedDebts = $this->debtCollectionRepository->listDebtCollectionsByUser(
            user: $currentUser,
            confirmationStatus: DebtCollectionConfirmationStatus::PENDING
        );

        /** @var DebtCollectionDto[] $unconfirmedDebts */
        $unconfirmedDebts = array_map(function (DebtCollection $debtCollection) use ($currentUser) {
            $isDebtor = $debtCollection->getDebtor() === $currentUser;
            return DebtCollectionDto::fromDebtCollection($debtCollection, $isDebtor);
        }, $unconfirmedDebts);

        $response = (new DebtCollectionStatisticsResponseDto())
            ->setTotalDebtorAmount($totalDebtorAmount)
            ->setTotalCreditorAmount($totalCreditorAmount)
            ->setUnconfirmedDebts($unconfirmedDebts);

        $apiResponse = new ApiDataResponse($response, "Debt Collection Statistics retrieved!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(
        #[CurrentUser] User $currentUser,
        #[MapQueryParameter] DebtCollectionStatus $status = null
    ): JsonResponse {
        $debts = $this->debtCollectionRepository->listDebtCollectionsByUser($currentUser, $status);

        /** @var DebtCollectionDto[] $debts */
        $debts = (new ArrayCollection($debts))
            ->map(function (DebtCollection $debtCollection) use ($currentUser) {
                $isDebtor = $debtCollection->getDebtor() === $currentUser;
                return DebtCollectionDto::fromDebtCollection($debtCollection, $isDebtor);
            })
            ->toArray();

        $apiResponse = new ApiDataResponse($debts, "Debt Collection records retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/list/creditors', name: 'list_creditors', methods: ['GET'])]
    public function listCreditors(
        #[CurrentUser] User $currentUser,
    ): JsonResponse {
        $debts = $this->debtCollectionRepository->findBy([
            'debtor' => $currentUser,
            'status' => [
                DebtCollectionStatus::UNPAID->value,
                DebtCollectionStatus::PARTIAL->value,
                DebtCollectionStatus::PAID->value,
            ],
            'confirmationStatus' => DebtCollectionConfirmationStatus::ACCEPTED,
        ], ['createdAt' => 'DESC']);

        /** @var DebtCollectionDto[] $debts */
        $debts = (new ArrayCollection($debts))
            ->map(function (DebtCollection $debtCollection) use ($currentUser) {
                $isDebtor = $debtCollection->getDebtor() === $currentUser;
                return DebtCollectionDto::fromDebtCollection($debtCollection, $isDebtor);
            })
            ->toArray();

        $apiResponse = new ApiDataResponse($debts, "Debt Collection records retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/list/creditors-as-witness', name: 'list_creditors_as_witness', methods: ['GET'])]
    public function listCreditorsAsWitness(
        #[CurrentUser] User $currentUser,
        WitnessBindingRepository $witnessBindingRepository
    ): JsonResponse {
        //        $qb = $witnessBindingRepository->createQueryBuilder('witnessBinding')
//            ->select('witnessBinding, debt')
//            ->join('witnessBinding.user', 'debtor') // Join the debtor relation
//            ->join('debtor.debtCollections', 'debt') // Join the debts collection
//            ->where('witnessBinding.witness = :currentUser')
//            ->andWhere('debt.status IN (:statuses)')
//            ->setParameter('currentUser', $currentUser)
//            ->setParameter('statuses', [
//                DebtCollectionStatus::UNPAID->value,
//                DebtCollectionStatus::PARTIAL->value,
//            ])
        /**
         *
         * LEFT JOIN App\Entity\User debtor
         * LEFT JOIN App\Entity\DebtCollection debt
         */
        $dql = "
            SELECT debt
            FROM App\Entity\DebtCollection debt
            JOIN debt.debtor debtor
            JOIN App\Entity\WitnessBinding witnessBinding WITH witnessBinding.user = debtor
            WHERE witnessBinding.witness = :currentUser
            AND debt.status IN (:statuses)
            AND debt.confirmationStatus = :confirmationStatus
            ORDER BY debt.createdAt DESC
        ";

        $query = $this->entityManager->createQuery($dql)
            ->setParameter('currentUser', $currentUser)
            ->setParameter('statuses', [
                DebtCollectionStatus::UNPAID->value,
                DebtCollectionStatus::PARTIAL->value,
                DebtCollectionStatus::PAID->value,
            ])
            ->setParameter('confirmationStatus', DebtCollectionConfirmationStatus::ACCEPTED);

        $debts = $query->getResult();

        /** @var DebtCollectionDto[] $debts */
        $debts = (new ArrayCollection($debts))
            ->map(function (DebtCollection $debtCollection) use ($currentUser) {
                $isDebtor = $debtCollection->getDebtor() === $currentUser;
                return DebtCollectionDto::fromDebtCollection($debtCollection, $isDebtor);
            })
            ->toArray();

        $apiResponse = new ApiDataResponse($debts, "Debt Collection records retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/list/debtors-as-witness', name: 'list_debtors_as_witness', methods: ['GET'])]
    public function listDebtorsAsWitness(
        #[CurrentUser] User $currentUser,
    ): JsonResponse {
        $dql = "
            SELECT debt
            FROM App\Entity\DebtCollection debt
            JOIN debt.creditor creditor
            JOIN App\Entity\WitnessBinding witnessBinding WITH witnessBinding.user = creditor
            WHERE witnessBinding.witness = :currentUser
            AND debt.status IN (:statuses)
            AND debt.confirmationStatus = :confirmationStatus
            ORDER BY debt.createdAt DESC
        ";

        $query = $this->entityManager->createQuery($dql)
            ->setParameter('currentUser', $currentUser)
            ->setParameter('statuses', [
                DebtCollectionStatus::UNPAID->value,
                DebtCollectionStatus::PARTIAL->value,
                DebtCollectionStatus::PAID->value,
            ])
            ->setParameter('confirmationStatus', DebtCollectionConfirmationStatus::ACCEPTED);

        $debts = $query->getResult();

        /** @var DebtCollectionDto[] $debts */
        $debts = (new ArrayCollection($debts))
            ->map(function (DebtCollection $debtCollection) use ($currentUser) {
                return DebtCollectionDto::fromDebtCollection($debtCollection, false);
            })
            ->toArray();

        $apiResponse = new ApiDataResponse($debts, "Debt Collection records retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/list/debtors', name: 'list_debtors', methods: ['GET'])]
    public function listDebtors(
        #[CurrentUser] User $currentUser,
    ): JsonResponse {
        $debts = $this->debtCollectionRepository->findBy([
            'creditor' => $currentUser,
            'status' => [
                DebtCollectionStatus::UNPAID->value,
                DebtCollectionStatus::PARTIAL->value,
                DebtCollectionStatus::PAID->value,
            ],
            'confirmationStatus' => DebtCollectionConfirmationStatus::ACCEPTED,
        ], ['createdAt' => 'DESC']);

        /** @var DebtCollectionDto[] $debts */
        $debts = (new ArrayCollection($debts))
            ->map(function (DebtCollection $debtCollection) use ($currentUser) {
                $isDebtor = $debtCollection->getDebtor() === $currentUser;
                return DebtCollectionDto::fromDebtCollection($debtCollection, $isDebtor);
            })
            ->toArray();

        $apiResponse = new ApiDataResponse($debts, "Debt Collection records retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/collection/{uid}', name: 'collection_details', methods: ['GET'])]
    public function find(string $uid, #[CurrentUser] User $currentUser): JsonResponse
    {
        $debtCollection = $this->debtCollectionRepository->findOneBy([
            'uid' => $uid,
        ]);
        if (!$debtCollection) {
            throw new NotFoundException("Debt Collection not found!");
        }

        $isDebtor = $debtCollection->getDebtor() === $currentUser;
        $responseDto = DebtCollectionDto::fromDebtCollection($debtCollection, $isDebtor);

        $apiResponse = new ApiDataResponse($responseDto, "Debt Collection retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/collection/{uid}/payments', name: 'collection_list_payments', methods: ['GET'])]
    public function listCollectionPayments(
        string $uid,
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $debtCollection = $this->debtCollectionRepository->findOneBy([
            'uid' => $uid,
        ]);
        if (!$debtCollection) {
            throw new NotFoundException("Debt Collection not found!");
        }

        $payments = $this->debtCollectionPaymentRepository->findBy([
            'debtCollection' => $debtCollection,
        ], ['createdAt' => 'DESC']);
        /** @var DebtCollectionPaymentDto[] $payments */
        $payments = (new ArrayCollection($payments))
            ->map(fn(DebtCollectionPayment $payment) => DebtCollectionPaymentDto::fromDebtCollectionPayment($payment))
            ->toArray();

        $apiResponse = new ApiDataResponse($payments, "Debt Collection Payments retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/collection-all-payments', name: 'collection_list_all_payments', methods: ['GET'])]
    public function listAllCollectionPayments(
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $payments = $this->debtCollectionPaymentRepository
            ->listAllCollectionPaymentByUser($currentUser);
        /** @var DebtCollectionPaymentDto[] $payments */
        $payments = (new ArrayCollection($payments))
            ->map(fn(DebtCollectionPayment $payment) => DebtCollectionPaymentDto::fromDebtCollectionPayment($payment))
            ->toArray();

        $apiResponse = new ApiDataResponse($payments, "Debt Collection Payments retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }


    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addDebt(
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] AddDebtRequestDto $requestDto
    ): JsonResponse {
        return $this->entityManager->wrapInTransaction(function() use ($currentUser, $requestDto) {
            $otherPartyUser = $this->userRepository->findOneBy([
                'phoneNumber' => $requestDto->getPhoneNumber(),
            ]);
            if (!$otherPartyUser) {
                throw new BadRequestException("Other party user doesn't exist");
            }

            $otherPartyType = DaynPartyType::tryFrom($requestDto->getType());
            if (!$otherPartyType) {
                throw new BadRequestException("Other party type doesn't exist! Valid values: (debtor, creditor)");
            }

            $creditor = null;
            $debtor = null;
            if ($otherPartyType === DaynPartyType::CREDITOR) {
                $creditor = $otherPartyUser;
                $debtor = $currentUser;
            } elseif ($otherPartyType === DaynPartyType::DEBTOR) {
                $debtor = $otherPartyUser;
                $creditor = $currentUser;
            }

            assert($creditor !== null);
            assert($debtor !== null);

            $amountOwed = MoneyUtil::toKobo($requestDto->getAmount());
            $debtCollection = (new DebtCollection())
                ->setUid(UidUtils::generateUid())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setLastModifiedAt(new \DateTimeImmutable())
                ->setAmount((string) $amountOwed)
                ->setAmountUnpaid($amountOwed)
                ->setPhoneNumber($requestDto->getPhoneNumber())
                ->setDescription($requestDto->getDescription())
                ->setStatus(DebtCollectionStatus::UNPAID->value)
                ->setConfirmationStatus(DebtCollectionConfirmationStatus::PENDING)
                ->setCreatedBy($currentUser)
                ->setDebtor($debtor)
                ->setCreditor($creditor);
            $this->entityManager->persist($debtCollection);
            $this->entityManager->flush();

            $currentUserDisplayName = $currentUser->getFullName();
            $otherPartyUserPhoneNumber = $otherPartyUser->getPhoneNumber();
            $debtCollectionAmountNaira = MoneyUtil::toNaira($debtCollection->getAmount());
            $message = match ($otherPartyType) {
                DaynPartyType::CREDITOR => "Hello, {$currentUserDisplayName} added you as a creditor for a debt of ₦{$debtCollectionAmountNaira} on Dayn App.",
                DaynPartyType::DEBTOR => "Hello, {$currentUserDisplayName} added you as a debtor for a debt of ₦{$debtCollectionAmountNaira} on Dayn App."
            };

            $result = $this->smsService->sendMessage($otherPartyUserPhoneNumber, $message);
            if (!$result) {
                $this->logger->error("Unable to send SMS to new debt attache: $otherPartyUserPhoneNumber");
            }

            $this->eventDispatcher->dispatch(new NewDebtCollectionEvent($debtCollection->getUid()));

            $isDebtor = $debtCollection->getDebtor() === $currentUser;
            $responseData = DebtCollectionDto::fromDebtCollection($debtCollection, $isDebtor);

            $apiResponse = new ApiDataResponse($responseData, "Debt added successfully!");

            return $this->json($apiResponse, Response::HTTP_CREATED);
        });
    }

    #[Route('/collection/{uid}/confirm', name: 'collection_confirm', methods: ['POST'])]
    public function acceptDebt(
        string $uid,
        #[MapRequestPayload] ConfirmDebtRequestDto $requestDto,
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $debtCollection = $this->debtCollectionRepository->findOneBy([
            'uid' => $uid,
        ]);
        if (!$debtCollection) {
            throw new NotFoundException("Debt Collection not found!");
        }

        $status = DebtCollectionConfirmationStatus::tryFrom($requestDto->status);
        if (!$status) {
            throw new BadRequestException("Invalid Debt collection status!");
        }

        if ($debtCollection->getConfirmationStatus() !== DebtCollectionConfirmationStatus::PENDING) {
            throw new BadRequestException("This debt collection record has already been confirmed!");
        }

        if ($debtCollection->getCreatedBy()->getUid() === $currentUser->getUid() && $status !== DebtCollectionConfirmationStatus::CANCELLED) {
            $this->logger->error("User {$currentUser->getUid()} tried to ACCEPT their own created debt {$debtCollection->getUid()}");
            throw new BadRequestException("Invalid Operation! Only author can cancel a debt record!");
        }

        // Allow debtor, creditor, or creator (if cancelling)
        $isDebtor = $debtCollection->getDebtor()->getUid() === $currentUser->getUid();
        $isCreditor = $debtCollection->getCreditor()->getUid() === $currentUser->getUid();

        if (!$isDebtor && !$isCreditor) {
            $this->logger->error("User {$currentUser->getUid()} tried to confirm debt {$debtCollection->getUid()} but is not a party (Debtor: {$debtCollection->getDebtor()->getUid()}, Creditor: {$debtCollection->getCreditor()->getUid()})");
            throw new BadRequestException("Invalid Operation. You are not a party to this debt record!");
        }

        $debtCollection->setConfirmationStatus($status)
            ->setConfirmationStatusMessage($requestDto->confirmationStatusMessage)
            ->setLastModifiedAt(new \DateTimeImmutable());
        $this->entityManager->persist($debtCollection);
        $this->entityManager->flush();

        // @TODO: Send email to debt collection creator that debt collection was confirmed!

        $isDebtor = $debtCollection->getDebtor() === $currentUser;
        $responseData = DebtCollectionDto::fromDebtCollection($debtCollection, $isDebtor);

        $message = match ($status) {
            DebtCollectionConfirmationStatus::ACCEPTED => "Debt record accepted!",
            DebtCollectionConfirmationStatus::REJECTED => "Debt record rejected!",
            DebtCollectionConfirmationStatus::CANCELLED => "Debt record cancelled!",
            default => "Action completed!",
        };

        $apiResponse = new ApiDataResponse($responseData, $message);

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/payment/compute-fees', name: 'payment_compute_fees', methods: ['GET'])]
    public function computeFees(
        #[MapQueryParameter] int $amount,
        #[MapQueryParameter] string $provider = 'monnify'
    ): JsonResponse {
        $paymentProvider = PaymentProvider::tryFrom($provider) ?? PaymentProvider::Monnify;
        $processor = $this->paymentProcessorProvider->getProcessor($paymentProvider);

        if (!$processor) {
            throw new BadRequestException("Payment provider not supported or unavailable.");
        }

        // Amount comes from Flutter as Kobo, needs to be Naira for processor
        $amountNaira = $amount / 100;
        $feeDto = $processor->computeTransactionFeeFromNairaAmount((int)$amountNaira);
        
        // Convert fee back to Kobo for Flutter DTO consistency (int)
        $feeDto->setTransactionFee($feeDto->getTransactionFee() * 100);

        return $this->json(new ApiDataResponse($feeDto), Response::HTTP_OK);
    }

    #[Route('/collection/{uid}/pay/psp', name: 'collection_pay_psp', methods: ['GET', 'POST'])]
    public function initiatePspPayment(
        string $uid,
        Request $request,
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] ?DebtPayInitiateRequestDto $requestDto = null
    ): JsonResponse {
        if ($request->isMethod('GET')) {
            throw new BadRequestException("This endpoint only accepts POST requests. If you are seeing this, your app sent a GET request, likely due to a server-side redirect (e.g. from http to https or a trailing slash issue).");
        }
        
        if (!$requestDto) {
             throw new BadRequestException("Missing request payload.");
        }
        $debtCollection = $this->debtCollectionRepository->findOneBy(['uid' => $uid]);
        if (!$debtCollection) {
            throw new NotFoundException("Debt Collection not found!");
        }

        if ($debtCollection->getDebtor() !== $currentUser) {
            throw new BadRequestException("Invalid Operation. You are not the debtor of this collection!");
        }

        // Amount comes from Flutter as Kobo
        $amountKobo = (int) $requestDto->getAmount();
        $amountNaira = $amountKobo / 100;
        
        $paymentProvider = PaymentProvider::Monnify; // Use Monnify by default
        $paymentProcessor = $this->paymentProcessorProvider->getProcessor($paymentProvider);

        if (!$paymentProcessor) {
            throw new BadRequestException("Payment provider not supported or unavailable.");
        }

        // 2. Compute Fees (already in Naira from processor)
        $feeData = $paymentProcessor->computeTransactionFeeFromNairaAmount((int)$amountNaira);
        $totalAmountNaira = (float)$amountNaira + (float)$feeData->getTransactionFee();
        $totalAmountKobo = MoneyUtil::toKobo($totalAmountNaira);

        // 3. Initialize Transaction
        $reference = UidUtils::generateUid();
        
        // Generate a unique email alias to bypass "1 account per customer" limit in Monnify
        // Format: original+timestamp@domain (e.g. user+1700000000@gmail.com)
        $username = $currentUser->getUsername();
        if (str_contains($username, '@')) {
            $emailParts = explode('@', $username);
            $aliasSuffix = '+' . time();
            $uniqueMonnifyEmail = $emailParts[0] . $aliasSuffix . '@' . $emailParts[1];
        } else {
            $uniqueMonnifyEmail = $username . '+' . time() . '@daynapp.com';
        }

        $virtualAccountRequest = (new CreateTransactionalAccountRequestDto())
            ->setTransactionReference($reference)
            ->setAmount((float)$totalAmountNaira)
            ->setCustomerName($currentUser->getFullName()) // Debtor's name
            ->setCustomerEmail($uniqueMonnifyEmail);

        $gatewayResponse = $paymentProcessor->createTransactionalAccount($virtualAccountRequest);

        if (!$gatewayResponse) {
            throw new BadRequestException("Unable to initiate transaction.");
        }

        // 4. Persist PaymentTransactionalAccount
        $transactionalAccount = (new PaymentTransactionalAccount())
            ->setUid(UidUtils::generateUid())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setLastModifiedAt(new \DateTimeImmutable())
            ->setAccountNumber($gatewayResponse->getAccountNumber())
            ->setAccountName($gatewayResponse->getAccountName() ?? "Dayn Virtual Account")
            ->setBankName($gatewayResponse->getBankName())
            ->setBankCode($gatewayResponse->getBankCode())
            ->setCustomerName($currentUser->getFullName())
            ->setAmountSpecified((string)$amountKobo)
            ->setAmountCharged((string)$totalAmountKobo)
            ->setFee((string)MoneyUtil::toKobo($feeData->getTransactionFee()))
            ->setFeeRate((string)$feeData->getFeeRate())
            ->setFeeType($feeData->getFeeType())
            ->setProvider($paymentProvider)
            ->setProviderReference($gatewayResponse->getProviderAccountReference())
            ->setProviderResponseObject($this->serializer->normalize($gatewayResponse))
            ->setTransactionReference($reference);
        $this->entityManager->persist($transactionalAccount);

        // 5. Create pending DebtCollectionPayment record
        $debtPayment = (new DebtCollectionPayment())
            ->setUid(UidUtils::generateUid())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setLastModifiedAt(new \DateTimeImmutable())
            ->setDebtCollection($debtCollection)
            ->setAmount((string)$amountKobo)
            ->setChannel(DebtPaymentChannel::PSP->value)
            ->setStatus(DebtCollectionPaymentStatus::PENDING->value)
            ->setPaymentReference($reference)
            ->setCreatedBy($currentUser);
        $this->entityManager->persist($debtPayment);

        $this->entityManager->flush();

        $responseDto = new InitiatePspPaymentResponseDto(
            authorizationUrl: null, 
            accessCode: $gatewayResponse->getBankName(),
            reference: $reference,
            accountNumber: $gatewayResponse->getAccountNumber(),
            accountName: $gatewayResponse->getAccountName() ?? "Dayn Virtual Account",
            bankName: $gatewayResponse->getBankName(),
            bankCode: $gatewayResponse->getBankCode(),
            amountToPay: (string) $totalAmountKobo,
            createdAt: DateTimeUtils::getDateTimeNow()->format(\DateTime::RFC3339)
        );

        return $this->json(new ApiDataResponse($responseDto, "Payment Initiated"), Response::HTTP_OK);
    }

    #[Route('/collection/{uid}/pay/card', name: 'collection_pay_card', methods: ['GET', 'POST'])]
    public function initiateCardPayment(
        string $uid,
        Request $request,
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] ?DebtPayInitiateRequestDto $requestDto = null
    ): JsonResponse {
        if ($request->isMethod('GET')) {
            throw new BadRequestException("This endpoint only accepts POST requests. If you are seeing this, your app sent a GET request, likely due to a server-side redirect (e.g. from http to https or a trailing slash issue).");
        }

        if (!$requestDto) {
             throw new BadRequestException("Missing request payload.");
        }
        $debtCollection = $this->debtCollectionRepository->findOneBy(['uid' => $uid]);
        if (!$debtCollection) {
            throw new NotFoundException("Debt Collection not found!");
        }

        if ($debtCollection->getDebtor() !== $currentUser) {
            throw new BadRequestException("Invalid Operation. You are not the debtor of this collection!");
        }

        $amountKobo = (int) $requestDto->getAmount();
        $amountNaira = $amountKobo / 100;
        
        $paymentProvider = PaymentProvider::Monnify;
        $paymentProcessor = $this->paymentProcessorProvider->getProcessor($paymentProvider);

        if (!$paymentProcessor) {
            throw new BadRequestException("Payment provider not supported or unavailable.");
        }

        $feeData = $paymentProcessor->computeTransactionFeeFromNairaAmount((int)$amountNaira);
        $totalAmountNaira = $amountNaira + $feeData->getTransactionFee();
        $reference = UidUtils::generateUid();
        
        $username = $currentUser->getUsername();
        if (str_contains($username, '@')) {
            $emailParts = explode('@', $username);
            $aliasSuffix = '+' . time();
            $uniqueMonnifyEmail = $emailParts[0] . $aliasSuffix . '@' . $emailParts[1];
        } else {
            $uniqueMonnifyEmail = $username . '+' . time() . '@daynapp.com';
        }

        $requestDtoForProcessor = (new CreateTransactionalAccountRequestDto())
            ->setTransactionReference($reference)
            ->setAmount((float)$totalAmountNaira)
            ->setCustomerName($currentUser->getFullName())
            ->setCustomerEmail($uniqueMonnifyEmail);

        $gatewayResponse = $paymentProcessor->initializeTransaction($requestDtoForProcessor); // Returns array

        if (!$gatewayResponse || !isset($gatewayResponse['checkoutUrl'])) {
            throw new BadRequestException("Unable to initiate card transaction.");
        }

        // Persist PaymentTransactionalAccount
        $transactionalAccount = (new PaymentTransactionalAccount())
            ->setUid(UidUtils::generateUid())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setLastModifiedAt(new \DateTimeImmutable())
            ->setAccountNumber("CARD") // No account number for card
            ->setAccountName("Card Payment")
            ->setBankName($paymentProvider->value)
            ->setBankCode("CARD")
            ->setCustomerName($currentUser->getFullName())
            ->setAmountSpecified((string)$amountKobo)
            ->setAmountCharged((string)MoneyUtil::toKobo($totalAmountNaira))
            ->setFee((string)MoneyUtil::toKobo($feeData->getTransactionFee()))
            ->setFeeRate((string)$feeData->getFeeRate())
            ->setFeeType($feeData->getFeeType())
            ->setProvider($paymentProvider)
            ->setProviderReference($gatewayResponse['transactionReference'] ?? $reference)
            ->setProviderResponseObject($gatewayResponse)
            ->setTransactionReference($reference);
        $this->entityManager->persist($transactionalAccount);

        // Create pending DebtCollectionPayment record
        $debtPayment = (new DebtCollectionPayment())
            ->setUid(UidUtils::generateUid())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setLastModifiedAt(new \DateTimeImmutable())
            ->setDebtCollection($debtCollection)
            ->setAmount((string)$amountKobo)
            ->setChannel(DebtPaymentChannel::PSP->value) // Using PSP as it is online payment
            ->setStatus(DebtCollectionPaymentStatus::PENDING->value)
            ->setPaymentReference($reference)
            ->setCreatedBy($currentUser);
        $this->entityManager->persist($debtPayment);

        $this->entityManager->flush();

        // Return Checkout URL
        $data = [
            'checkoutUrl' => $gatewayResponse['checkoutUrl'],
            'reference' => $reference,
            'amountToPay' => (string) MoneyUtil::toKobo($totalAmountNaira),
        ];

        return $this->json(new ApiDataResponse($data, "Card Payment Initiated"), Response::HTTP_OK);
    }

    #[Route('/payment/verify/{reference}', name: 'collection_verify_payment', methods: ['GET'])]
    public function verifyPspPayment(
        string $reference,
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $paymentProvider = PaymentProvider::Monnify;
        $paymentProcessor = $this->paymentProcessorProvider->getProcessor($paymentProvider);

        if (!$paymentProcessor) {
            throw new BadRequestException("Payment Service Provider currently not available!");
        }

        $verifyResponse = $paymentProcessor->verifyTransaction($reference);
        if (!$verifyResponse) {
            throw new BadRequestException("Unable to verify transaction.");
        }

        // Check if successfully paid on gateway
        // Note: GenericPaymentProcessorTransactionStatus is expected but we check SUCCESSFUL
        if ($verifyResponse->getTransactionStatus() !== \App\Model\Integration\Common\PaymentProcessor\GenericPaymentProcessorTransactionStatus::SUCCESSFUL) {
             return $this->json(new ApiDataResponse(null, "Transaction status: " . $verifyResponse->getTransactionStatus()->value), Response::HTTP_OK);
        }

        // Finding the linked debt via transactional account reference
        // In this implementation we assume the reference is unique to the funding/payment request
        return $this->json(new ApiDataResponse(null, "Payment verified manually. Please refresh to see updates."), Response::HTTP_OK);
    }

    #[Route('/collection/{uid}/pay/initiate', name: 'collection_pay', methods: ['POST'])]
    public function initiatePayment(
        string $uid,
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] DebtPayInitiateRequestDto $requestDto,
    ): JsonResponse {
        return $this->entityManager->wrapInTransaction(function() use ($uid, $currentUser, $requestDto) {
            try {
                $debtCollection = $this->debtCollectionRepository->findOneBy([
                    'uid' => $uid,
                ]);
                if (!$debtCollection) {
                    throw new NotFoundException("Debt Collection not found!");
                }

                if ($debtCollection->getDebtor() !== $currentUser) {
                    throw new BadRequestException("Invalid Operation. You can't pay yourself!");
                }

                if ($debtCollection->getConfirmationStatus() !== DebtCollectionConfirmationStatus::ACCEPTED) {
                    throw new BadRequestException("The debt hasn't been confirmed by the creditor!");
                }

                $amount = (int) $requestDto->getAmount();
                $amountUnpaid = $debtCollection->getAmountUnpaid();

                if ($debtCollection->getStatus() === DebtCollectionStatus::PAID->value) {
                    throw new BadRequestException("Debt Collection already paid!");
                }

                $channel = DebtPaymentChannel::tryFrom($requestDto->getChannel());
                if ($amount > $amountUnpaid) {
                    throw new BadRequestException("Amount to be paid is greater than unpaid amount!");
                }

                $pendingCollectionPayments = $this->debtCollectionPaymentRepository->findBy([
                    'debtCollection' => $debtCollection,
                    'status' => DebtCollectionPaymentStatus::PENDING->value,
                ]);

                $totalPendingSum = 0.0;
                foreach ($pendingCollectionPayments as $pendingCollectionPayment) {
                    $totalPendingSum += (float) $pendingCollectionPayment->getAmount();
                }

                if (((float) $debtCollection->getAmountUnpaid() - $totalPendingSum) < $amount) {
                    throw new BadRequestException("Can not proceed as there are pending cash payments that exceed the amount specified!");
                }

                if ($channel === null) {
                    throw new BadRequestException("Invalid Payment Channel!");
                }

                $debtCollectionPaymentStatus = DebtCollectionPaymentStatus::PENDING;
                $paymentReference = null;
                if ($channel === DebtPaymentChannel::WALLET) {
                    $senderWallet = $currentUser->getWallet();
                    
                    // LOCKING: Prevent concurrent balance updates
                    $this->entityManager->lock($senderWallet, LockMode::PESSIMISTIC_WRITE);
                    $this->entityManager->refresh($senderWallet);
                    
                    if ($amount > (float)$senderWallet->getBalance()) {
                        throw new BadRequestException("Amount is greater than available wallet balance!");
                    }

                    $receivingWallet = $debtCollection->getCreditor()->getWallet();
                    // LOCKING: Receiver wallet as well
                    $this->entityManager->lock($receivingWallet, LockMode::PESSIMISTIC_WRITE);
                    $this->entityManager->refresh($receivingWallet);

                    $walletTransfer = (new WalletTransfer())
                        ->setAmount((string) $amount)
                        ->setReference(RandomUtils::generateWalletTransferReference())
                        ->setSenderWallet($senderWallet)
                        ->setReceiverWallet($receivingWallet);
                    $this->entityManager->persist($walletTransfer);

                    // Update Sender Balance (Decrease)
                    $senderNewBalance = (float)$senderWallet->getBalance() - $amount;
                    $senderWallet->setBalance((string)$senderNewBalance);
                    $this->entityManager->persist($senderWallet);

                    // Update Receiver Balance (Increase)
                    $receiverNewBalance = (float)$receivingWallet->getBalance() + $amount;
                    $receivingWallet->setBalance((string)$receiverNewBalance);
                    $this->entityManager->persist($receivingWallet);

                    $debtCollectionAmountUnpaid = $debtCollection->getAmountUnpaid() - $amount;
                    $debtCollectionStatus = $debtCollectionAmountUnpaid > 0 ? DebtCollectionStatus::PARTIAL : DebtCollectionStatus::PAID;
                    $debtCollection->setAmountUnpaid($debtCollectionAmountUnpaid);
                    $debtCollection->setStatus($debtCollectionStatus->value);
                    $this->entityManager->persist($debtCollection);

                    $debtCollectionPaymentStatus = DebtCollectionPaymentStatus::APPROVED;
                    $paymentReference = $walletTransfer->getReference();
                }

                $debtCollectionPayment = (new DebtCollectionPayment())
                    ->setUid(UidUtils::generateUid())
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setLastModifiedAt(new \DateTimeImmutable())
                    ->setAmount((string) $amount)
                    ->setChannel($channel->value)
                    ->setStatus($debtCollectionPaymentStatus->value)
                    ->setDebtCollection($debtCollection)
                    ->setPaymentReference($paymentReference)
                    ->setCreatedBy($currentUser);
                    
                if (method_exists($debtCollectionPayment, 'setIsAcknowledged')) {
                    $debtCollectionPayment->setIsAcknowledged(false);
                }
                    
                $this->entityManager->persist($debtCollectionPayment);
                $this->entityManager->flush();

                $responseMessage = match ($channel) {
                    DebtPaymentChannel::CASH => 'Payment initiated successfully. Awaiting acceptance...',
                    DebtPaymentChannel::WALLET => 'Payment made successfully via Wallet Transfer.',
                };

                $responseData = DebtCollectionPaymentDto::fromDebtCollectionPayment($debtCollectionPayment);
                $apiResponse = new ApiDataResponse($responseData, $responseMessage);

                return $this->json($apiResponse, Response::HTTP_CREATED);
            } catch (\Throwable $e) {
                $this->logger->error('Payment Initiation Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                if ($e instanceof BadRequestException || $e instanceof NotFoundException) {
                    throw $e;
                }
                throw new \App\Exception\Api\InternalServerException("Payment Initiation Error: " . $e->getMessage());
            }
        });
    }

    #[Route('/collection/{uid}/pay/complete', name: 'collection_complete', methods: ['POST'])]
    public function completePayment(
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] DebtPayCompleteRequestDto $requestDto
    ): JsonResponse {
        return $this->entityManager->wrapInTransaction(function() use ($currentUser, $requestDto) {
            try {
                $debtCollectionPayment = $this->debtCollectionPaymentRepository->findOneBy([
                    'uid' => $requestDto->getPaymentUid(),
                ]);
                if (!$debtCollectionPayment) {
                    throw new NotFoundException("Debt Collection Payment not found!");
                }

                $debtCollection = $debtCollectionPayment->getDebtCollection();
                if ($debtCollection->getCreditor() !== $currentUser && $requestDto->isAccepted()) {
                    throw new BadRequestException("Invalid Operation. Only the creditor can mark this payment as received!");
                }

                if ($debtCollection->getDebtor() === $currentUser && $requestDto->isAccepted()) {
                    throw new BadRequestException("Invalid Operation. You can't pay yourself!");
                }

                // Allow if PENDING OR (APPROVED and not acknowledged)
                if ($debtCollectionPayment->getStatus() !== DebtCollectionPaymentStatus::PENDING->value) {
                     if ($debtCollectionPayment->getStatus() === DebtCollectionPaymentStatus::APPROVED->value && 
                         (!$debtCollectionPayment->isAcknowledged())) {
                         // Proceed to just set Acknowledged = true
                     } else {
                        throw new BadRequestException("Debt Collection Payment already completed!");
                     }
                }

                if ($requestDto->isAccepted()) {
                    // Skip money logic if already APPROVED (Wallet)
                    if ($debtCollectionPayment->getStatus() === DebtCollectionPaymentStatus::PENDING->value) {
                        if ($debtCollection->getStatus() === DebtCollectionStatus::PAID->value) {
                            throw new BadRequestException("Debt Collection has already been paid for!");
                        }
        
                        // Using MoneyUtil for precision
                        $paymentAmount = (int) $debtCollectionPayment->getAmount();
                        $currentUnpaid = (int) $debtCollection->getAmountUnpaid();
                        $updatedAmountUnpaid = max(0, MoneyUtil::subtract($currentUnpaid, $paymentAmount));
                        
                        $debtCollectionStatus = $updatedAmountUnpaid > 0 ? DebtCollectionStatus::PARTIAL : DebtCollectionStatus::PAID;
                        $debtCollection->setAmountUnpaid($updatedAmountUnpaid);
                        $debtCollection->setStatus($debtCollectionStatus->value);
                        $this->entityManager->persist($debtCollection);
        
                        // CREDIT WALLET for Digital Payments (PSP/CARD) that were manually confirmed
                        // CASH is excluded as it's physical. WALLET is excluded as it was credited at initiation.
                        $channel = DebtPaymentChannel::tryFrom($debtCollectionPayment->getChannel());
                        if ($channel === DebtPaymentChannel::PSP || $channel === DebtPaymentChannel::PSP_CARD) {
                            $creditor = $debtCollection->getCreditor();
                            $creditorWallet = $creditor->getWallet();
                            
                            // LOCKING: Prevent concurrent balance updates
                            $this->entityManager->lock($creditorWallet, LockMode::PESSIMISTIC_WRITE);
                            $this->entityManager->refresh($creditorWallet);
                            
                            $newBalance = MoneyUtil::add($creditorWallet->getBalance(), $paymentAmount);
                            $creditorWallet->setBalance((string)$newBalance);
                            $this->entityManager->persist($creditorWallet);
                            
                            $this->logger->info("Creditor wallet credited manually for digital payment: {$debtCollectionPayment->getUid()}");
                        }
                    }
                    
                    $debtCollectionPaymentStatus = DebtCollectionPaymentStatus::APPROVED;
                    $debtCollectionPayment->setStatus($debtCollectionPaymentStatus->value);
                    $debtCollectionPayment->setLastModifiedAt(new \DateTimeImmutable());
                    
                    if (method_exists($debtCollectionPayment, 'setIsAcknowledged')) {
                        $debtCollectionPayment->setIsAcknowledged(true);
                    }
                    
                    $this->entityManager->persist($debtCollectionPayment);
                    
                    // If payment was PENDING and we just approved it, check if we need to clean up other pendings
                    if ($debtCollection->getStatus() === DebtCollectionStatus::PAID->value) {
                        $debtCollectionPayments = $this->debtCollectionPaymentRepository->findBy([
                            'debtCollection' => $debtCollection,
                            'status' => DebtCollectionPaymentStatus::PENDING->value,
                        ]);

                        if (count($debtCollectionPayments) > 0) {
                            foreach ($debtCollectionPayments as $debtCollectionPayment) {
                                $this->entityManager->remove($debtCollectionPayment);
                            }
                        }
                    }

                } else {
                    $this->entityManager->remove($debtCollectionPayment);
                }

                $this->entityManager->flush();

                $responseData = DebtCollectionPaymentDto::fromDebtCollectionPayment($debtCollectionPayment);
                $apiResponse = new ApiDataResponse($responseData, "Debt Collection Payment completed!");

                return $this->json($apiResponse, Response::HTTP_OK);
            } catch (\Throwable $e) {
                $this->logger->error('Payment Completion Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                if ($e instanceof BadRequestException || $e instanceof NotFoundException) {
                    throw $e;
                }
                throw new \App\Exception\Api\InternalServerException("Payment Completion Error: " . $e->getMessage());
            }
        });
    }
}
