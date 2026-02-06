<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\WitnessBinding;
use App\Exception\Api\BadRequestException;
use App\Exception\Api\NotFoundException;
use App\Model\DTO\UserWitnessBindingDto;
use App\Model\Request\AddWitnessRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Repository\UserRepository;
use App\Repository\WitnessBindingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/witness', name: 'api_witness_')]
class WitnessController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WitnessBindingRepository $witnessBindingRepository,
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(
        #[CurrentUser] User $currentUser
    ): Response
    {
        $witnesses = $currentUser->getWitnessBindings()
            ->map(fn (WitnessBinding $witnessBinding) => UserWitnessBindingDto::fromWitnessBinding($witnessBinding))
            ->toArray();

        $apiResponse = new ApiDataResponse($witnesses, "Witnesses retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] AddWitnessRequestDto $requestDto
    ): JsonResponse
    {
        $witness = $this->userRepository->findOneBy([
            'phoneNumber' => $requestDto->getPhoneNumber(),
        ]);
        if ($witness === null) {
            throw new BadRequestException('Witness not registered on Dayn!');
        }

        $existingWitness = $this->witnessBindingRepository->findOneBy([
            'user' => $currentUser,
            'witness' => $witness,
        ]);
        if ($existingWitness) {
            throw new BadRequestException('Witness already exists!');
        }

        $witnessBinding = (new WitnessBinding())
            ->setWitness($witness)
            ->setUser($currentUser);
        $this->entityManager->persist($witnessBinding);
        $this->entityManager->flush();

        $responseData = UserWitnessBindingDto::fromWitnessBinding($witnessBinding);
        $apiResponse = new ApiDataResponse($responseData, "Witness added successfully!");

        return $this->json($apiResponse, Response::HTTP_CREATED);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        #[CurrentUser] User $currentUser,
        int $id
    ): JsonResponse
    {
        $witnessBinding = $this->witnessBindingRepository->findOneBy([
            'user' => $currentUser,
            'id' => $id,
        ]);
        if ($witnessBinding === null) {
            throw new NotFoundException('Witness record not found!');
        }

        $this->entityManager->persist($witnessBinding);
        $this->entityManager->flush();

        $apiResponse = new ApiDataResponse(null, "Witness record deleted successfully!");
        return $this->json($apiResponse, Response::HTTP_OK);
    }
}
