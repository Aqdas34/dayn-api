<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserBankAccount;
use App\Entity\UserInvitation;
use App\Exception\Api\BadRequestException;
use App\Exception\Api\ConflictException;
use App\Model\DTO\UserBankAccountDto;
use App\Model\Request\CheckUserAvailabilityByPhoneRequestDto;
use App\Model\Request\InviteUserByPhoneNumberRequestDto;
use App\Model\Request\ProfileInfoResponseDto;
use App\Model\Request\ProfileSetTransactionPinRequestDto;
use App\Model\Request\ProfileUpdateTransactionPinRequestDto;
use App\Model\Request\UpdateProfileRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Repository\UserInvitationRepository;
use App\Repository\UserRepository;
use App\Service\SmsService;
use App\Util\HashingUtil;
use App\Util\PhoneNumberUtil;
use App\Util\UidUtils;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/profile', name: 'api_profile_')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface   $entityManager,
        private readonly UserRepository           $userRepository,
        private readonly UserInvitationRepository $userInvitationRepository,
        private readonly LoggerInterface $logger,
        private readonly HashingUtil $hashingUtil,
    )
    {
    }

    #[Route('/info', name: 'fetch_info', methods: ['GET'])]
    public function fetchInfo(
        #[CurrentUser] User $currentUser,
    ): Response
    {
        $profile = (new ProfileInfoResponseDto())
            ->setUid($currentUser->getUid())
            ->setFirstName($currentUser->getFirstName())
            ->setLastName($currentUser->getLastName())
            ->setEmail($currentUser->getUserIdentifier())
            ->setPhoneNumber($currentUser->getPhoneNumber());

        $apiResponse = new ApiDataResponse($profile, "Profile retrieved successfully!");

        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/info', name: 'update_info', methods: ['POST'])]
    public function updateInfo(
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] UpdateProfileRequestDto $requestDto
    ): JsonResponse
    {
        $existingOtherUser = $this->userRepository->findOneBy([
            'username' => $requestDto->email,
        ]);
        if ($existingOtherUser && $existingOtherUser->getId() !== $currentUser->getId()) {
            throw new ConflictException("A user with this email already exists.");
        }

        $currentUser
            ->setFirstName($requestDto->firstName)
            ->setLastName($requestDto->lastName)
            ->setUsername($requestDto->email);
        $this->entityManager->persist($currentUser);
        $this->entityManager->flush();

        $responseData = (new ProfileInfoResponseDto())
            ->setUid($currentUser->getUid())
            ->setFirstName($currentUser->getFirstName())
            ->setLastName($currentUser->getLastName())
            ->setEmail($currentUser->getUserIdentifier())
            ->setPhoneNumber($currentUser->getPhoneNumber());
        $apiResponse = new ApiDataResponse($responseData, "Profile updated successfully!");
        return $this->json($apiResponse, Response::HTTP_OK);
    }

    #[Route('/check-availability/phone', name: 'check_availability_phone', methods: ['POST'])]
    public function checkPhoneAvailability(
        #[MapRequestPayload] CheckUserAvailabilityByPhoneRequestDto $requestDto
    ): JsonResponse
    {
        $user = $this->userRepository->findOneBy([
            'phoneNumber' => $requestDto->phoneNumber,
        ]);

        $userExists = $user !== null;

        $response = new ApiDataResponse([
            'user_exists' => $userExists
        ]);

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/user-invitation/by-phone', name: 'user_invitation_by_phone', methods: ['POST'])]
    public function inviteUserByPhoneNumber(
        #[MapRequestPayload] InviteUserByPhoneNumberRequestDto $requestDto,
        #[CurrentUser] User $currentUser,
        SmsService $smsService
    ): JsonResponse
    {
        $user = $this->userRepository->findOneBy([
            'phoneNumber' => $requestDto->phoneNumber,
        ]);
        if ($user) {
            throw new BadRequestException("A user with phone number: $requestDto->phoneNumber already exists.}");
        }

        $userInvitation = $this->userInvitationRepository->findOneBy([
            'phoneNumber' => $requestDto->phoneNumber,
            'user' => $currentUser,
        ]);
        if (!$userInvitation) {
            $userInvitation = (new UserInvitation())
                ->setPhoneNumber($requestDto->phoneNumber)
                ->setUser($currentUser);
            $invitationSent = false;
        } else {
            $invitationSent = $userInvitation->isInvitationSent();
            $this->logger->info("User Invitation Found. Value: $invitationSent");
        }

        if (!$invitationSent) {
            $namePhrase = "{$currentUser->getFullName()} - {$currentUser->getPhoneNumber()}";
            $fullPhoneNumber = PhoneNumberUtil::formatNigerianPhoneNumber($requestDto->phoneNumber);
            if (isset($requestDto->context['message_type'])) {
                $messageType = $requestDto->context['message_type'];
                $message = $messageType == "debtor" ? "$namePhrase owes you. " : "You owe $namePhrase. ";
            } else {
                $message = "You have been invited to Dayn!";
            }
            $message .= "Visit https://dayn.app to download the app for more details...";
            $result = $smsService->sendMessage($fullPhoneNumber, $message);
            $this->logger->info("Result of SMS: $result");
            $invitationSent = $result !== null;
        }

        $userInvitation->setInvitationSent($invitationSent);
        $this->entityManager->persist($userInvitation);
        $this->entityManager->flush();

        $response = new ApiDataResponse([
            'invitation_sent' => $invitationSent
        ]);

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/set-transaction-pin', name: 'set_transaction_pin', methods: ['POST'])]
    public function setTransactionPin(
        #[MapRequestPayload] ProfileSetTransactionPinRequestDto $requestDto,
        #[CurrentUser] User $currentUser
    ): JsonResponse
    {
        $hashedPin = $this->hashingUtil->hash($requestDto->pin);

        $wallet = $currentUser->getWallet();
        $wallet->setTransactionPin($hashedPin);
        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        $response = ApiDataResponse::fromData(null, "Transaction PIN set successfully.");

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/update-transaction-pin', name: 'update_transaction_pin', methods: ['POST'])]
    public function updateTransactionPin(
        #[MapRequestPayload] ProfileUpdateTransactionPinRequestDto $requestDto,
        #[CurrentUser] User $currentUser
    ): JsonResponse
    {
        $wallet = $currentUser->getWallet();
        if ($wallet->getTransactionPin() === null) {
            throw new BadRequestException("Transaction PIN not set. Please set it in your profile.");
        }
        $isPinValid = $this->hashingUtil->verify($wallet->getTransactionPin(), $requestDto->currentPin);
        if (!$isPinValid) {
            throw new BadRequestException("Invalid Transaction PIN.");
        }

        if ($requestDto->currentPin === $requestDto->newPin) {
            throw new BadRequestException("The new PIN can not be the same as the current PIN.");
        }

        $hashedPin = $this->hashingUtil->hash($requestDto->newPin);
        $wallet->setTransactionPin($hashedPin);
        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        $response = ApiDataResponse::fromData(null, "Transaction PIN updated successfully.");

        return $this->json($response, Response::HTTP_OK);
    }
}
