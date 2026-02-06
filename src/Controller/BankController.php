<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\Api\BadRequestException;
use App\Model\Request\BankVerifyAccountRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Service\BankService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/banks', name: 'api_banks_')]
class BankController extends AbstractController
{
    public function __construct(
        private readonly BankService $bankService
    )
    {
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $banks = $this->bankService->listBanks();

        $response = new ApiDataResponse($banks, "List of Banks retrieved!");

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/verify-bank-account', name: 'verify_bank_account', methods: ['POST'])]
    public function verifyBankAccount(
        #[MapRequestPayload] BankVerifyAccountRequestDto $requestDto
    ): JsonResponse
    {
        $response = $this->bankService->validateBankAccount($requestDto->getAccountNumber(), $requestDto->getBankCode());
        if ($response === null) {
            throw new BadRequestException("Verify Bank Account Failed!");
        }

        $response = new ApiDataResponse($response, "Bank Account Verified!");

        return $this->json($response, Response::HTTP_OK);
    }
}

