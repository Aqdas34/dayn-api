<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Beneficiary;
use App\Entity\User;
use App\Exception\Api\BadRequestException;
use App\Exception\Api\NotFoundException;
use App\Model\DTO\BeneficiaryDto;
use App\Model\Request\AddBeneficiaryRequestDto;
use App\Model\Request\UpdateBeneficiaryRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Repository\BeneficiaryRepository;
use App\Repository\UserRepository;
use App\Util\UidUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/beneficiary', name: 'api_beneficiary_')]
class BeneficiaryController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BeneficiaryRepository $beneficiaryRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $beneficiaries = $this->beneficiaryRepository->findByUserSorted($currentUser);

        $dtos = array_map(function (Beneficiary $beneficiary) {
            $isRegistered = $this->userRepository->findOneBy(['phoneNumber' => $beneficiary->getPhoneNumber()]) !== null;
            return BeneficiaryDto::fromBeneficiary($beneficiary, $isRegistered);
        }, $beneficiaries);

        return $this->json(ApiDataResponse::fromData($dtos, "Beneficiaries retrieved successfully"));
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] AddBeneficiaryRequestDto $requestDto
    ): JsonResponse {
        // Check if beneficiary with same phone already exists for this user
        $existing = $this->beneficiaryRepository->findOneBy([
            'user' => $currentUser,
            'phoneNumber' => $requestDto->getPhoneNumber()
        ]);

        if ($existing) {
            throw new BadRequestException("Beneficiary with this phone number already exists.");
        }

        $beneficiary = (new Beneficiary())
            ->setUid(UidUtils::generateUid())
            ->setDisplayName($requestDto->getDisplayName())
            ->setPhoneNumber($requestDto->getPhoneNumber())
            ->setCategory($requestDto->getCategory())
            ->setUser($currentUser);

        $this->entityManager->persist($beneficiary);
        $this->entityManager->flush();

        $isRegistered = $this->userRepository->findOneBy(['phoneNumber' => $beneficiary->getPhoneNumber()]) !== null;
        return $this->json(ApiDataResponse::fromData(
            BeneficiaryDto::fromBeneficiary($beneficiary, $isRegistered),
            "Beneficiary added successfully"
        ), Response::HTTP_CREATED);
    }

    #[Route('/{uid}/update', name: 'update', methods: ['PUT'])]
    public function update(
        string $uid,
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] UpdateBeneficiaryRequestDto $requestDto
    ): JsonResponse {
        $beneficiary = $this->beneficiaryRepository->findOneBy(['uid' => $uid, 'user' => $currentUser]);

        if (!$beneficiary) {
            throw new NotFoundException("Beneficiary not found.");
        }

        if ($requestDto->getDisplayName() !== null) {
            $beneficiary->setDisplayName($requestDto->getDisplayName());
        }

        if ($requestDto->getPhoneNumber() !== null) {
            // Check if another beneficiary with this phone exists
            $existing = $this->beneficiaryRepository->findOneBy([
                'user' => $currentUser,
                'phoneNumber' => $requestDto->getPhoneNumber()
            ]);

            if ($existing && $existing->getUid() !== $uid) {
                throw new BadRequestException("Another beneficiary with this phone number already exists.");
            }
            $beneficiary->setPhoneNumber($requestDto->getPhoneNumber());
        }

        if ($requestDto->getCategory() !== null) {
            $beneficiary->setCategory($requestDto->getCategory());
        }

        $this->entityManager->flush();

        $isRegistered = $this->userRepository->findOneBy(['phoneNumber' => $beneficiary->getPhoneNumber()]) !== null;
        return $this->json(ApiDataResponse::fromData(
            BeneficiaryDto::fromBeneficiary($beneficiary, $isRegistered),
            "Beneficiary updated successfully"
        ));
    }

    #[Route('/{uid}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(
        string $uid,
        #[CurrentUser] User $currentUser
    ): JsonResponse {
        $beneficiary = $this->beneficiaryRepository->findOneBy(['uid' => $uid, 'user' => $currentUser]);

        if (!$beneficiary) {
            throw new NotFoundException("Beneficiary not found.");
        }

        $this->entityManager->remove($beneficiary);
        $this->entityManager->flush();

        return $this->json(ApiDataResponse::fromData(null, "Beneficiary deleted successfully"));
    }
}
