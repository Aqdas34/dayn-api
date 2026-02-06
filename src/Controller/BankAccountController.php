<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserBankAccount;
use App\Event\UserBankAccountCreatedEvent;
use App\Exception\Api\NotFoundException;
use App\Model\DTO\UserBankAccountDto;
use App\Model\Request\AddBankAccountRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Repository\UserBankAccountRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/bank-accounts', name: 'api_bank_accounts_')]
class BankAccountController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserBankAccountRepository $bankAccountRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $bankAccounts = $user->getBankAccounts();
            $bankAccountsList = $bankAccounts
                ->map(fn (UserBankAccount $bankAccount) => UserBankAccountDto::fromUserBankAccount($bankAccount))
                ->toArray();

            $apiResponse = new ApiDataResponse($bankAccountsList, 'Bank Accounts retrieved successfully');

            return $this->json($apiResponse, Response::HTTP_OK);
        } catch (\Throwable $e) {
            return $this->json([
                'message' => 'Server Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/show/{id}', name: 'show', methods: ['GET'])]
    public function show(
        #[CurrentUser] User $currentUser,
        int $id
    ): JsonResponse
    {
        $bankAccount = $this->bankAccountRepository->findOneBy([
            'id' => $id,
            'user' => $currentUser,
        ]);
        if (!$bankAccount) {
            throw new NotFoundException("Bank Account not found!");
        }

        $responseData = UserBankAccountDto::fromUserBankAccount($bankAccount);
        $apiResponse = new ApiDataResponse($responseData, 'Bank Account retrieved successfully');

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(
        #[CurrentUser] User $user,
        #[MapRequestPayload] AddBankAccountRequestDto $requestDto
    ): JsonResponse
    {
        $bankAccount = (new UserBankAccount())
            ->setBankCode($requestDto->getBankCode())
            ->setBankName($requestDto->getBankName())
            ->setAccountNumber($requestDto->getAccountNumber())
            ->setAccountName($requestDto->getAccountName())
            ->setCurrency("NGN")
            ->setUser($user);
        $this->entityManager->persist($bankAccount);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new UserBankAccountCreatedEvent($bankAccount->getId()));

        $apiResponse = new ApiDataResponse(UserBankAccountDto::fromUserBankAccount($bankAccount), 'Bank Account created successfully');

        return $this->json($apiResponse, Response::HTTP_CREATED);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        #[CurrentUser] User $user,
        int $id
    ): JsonResponse
    {
        $bankAccount = $this->bankAccountRepository->findOneBy([
            'id' => $id,
            'user' => $user
        ]);
        if (!$bankAccount) {
            throw new NotFoundException("Bank Account not found!");
        }

        $this->entityManager->remove($bankAccount);
        $this->entityManager->flush();

        $apiResponse = new ApiDataResponse(null, "Bank Account deleted successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }
}
