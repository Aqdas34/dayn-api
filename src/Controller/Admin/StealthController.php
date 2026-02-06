<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Exception\Api\BadRequestException;
use App\Model\Request\AdminAuthorizationRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Service\BackgroundJobService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/stealth', name: 'admin_stealth_')]
class StealthController extends AbstractController
{
    public function __construct(
        private readonly BackgroundJobService $backgroundJobService
    )
    {
    }

    #[Route('/run-jobs', name: 'run_jobs')]
    public function runJobs(
        #[MapRequestPayload] AdminAuthorizationRequestDto $requestDto
    ): Response
    {
        if ($requestDto->authorizationCode !== "Admin@321") {
            throw new BadRequestException("Authorization code mismatch!");
        }

        try {
            $this->backgroundJobService->processPendingWalletPayouts();
            $this->backgroundJobService->processProcessingWalletFunding();
            $this->backgroundJobService->processProcessingWalletPayouts();
        } catch (\Throwable $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        return $this->json(new ApiDataResponse(null, "Background Jobs successful!"));
    }
}
