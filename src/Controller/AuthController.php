<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ForgotPasswordToken;
use App\Entity\User;
use App\Entity\UserAccessToken;
use App\Entity\UserBankAccount;
use App\Entity\UserWallet;
use App\Event\SendForgotPasswordOtpEmailEvent;
use App\Event\SendForgotPasswordSuccessEmailEvent;
use App\Event\UserBankAccountCreatedEvent;
use App\Exception\Api\BadRequestException;
use App\Exception\Api\ConflictException;
use App\Exception\Api\InvalidAuthenticationException;
use App\Model\Request\AuthAccessTokenRequestDto;
use App\Model\Request\AuthLoginRequestDto;
use App\Model\Request\AuthSignupRequestDto;
use App\Model\Request\ForgotPasswordFinalizeRequestDto;
use App\Model\Request\ForgotPasswordInitiateOtpRequestDto;
use App\Model\Request\ForgotPasswordVerifyOtpRequestDto;
use App\Model\Response\ApiDataResponse;
use App\Repository\ForgotPasswordTokenRepository;
use App\Repository\UserAccessTokenRepository;
use App\Repository\UserRepository;
use App\Service\BankService;
use App\Util\DateTimeUtils;
use App\Util\RandomUtils;
use App\Util\UidUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ForgotPasswordTokenRepository $forgotPasswordTokenRepository,
        private readonly UserRepository              $userRepository,
        private readonly UserAccessTokenRepository   $accessTokenRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly BankService $bankService,
    )
    {
    }

    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signup(
        #[MapRequestPayload] AuthSignupRequestDto $requestDto
    ): JsonResponse
    {
        $existingUser = $this->userRepository->findOneBy(['username' => $requestDto->email]);
        if ($existingUser) {
            throw new ConflictException("A user with the specified email already exists!");
        }

        $bank = $this->bankService->findBankByCode($requestDto->bankCode);
        if (!$bank) {
            throw new BadRequestException("The specified bank was not found!");
        }

        $user = new User();
        $user
            ->setFirstName($requestDto->firstName)
            ->setLastName($requestDto->lastName)
            ->setUsername($requestDto->email)
            ->setPhoneNumber($requestDto->phoneNumber)
            ->setPassword($this->passwordHasher->hashPassword($user, $requestDto->password));
        $this->entityManager->persist($user);

        $userWallet = (new UserWallet())
            ->setUser($user)
            ->setBalance("0");
        $this->entityManager->persist($userWallet);

        $userBankAccount = (new UserBankAccount())
            ->setUser($user)
            ->setCurrency("NGN")
            ->setAccountNumber($requestDto->accountNumber)
            ->setAccountName($requestDto->accountName)
            ->setBankName($bank->getName())
            ->setBankCode($bank->getCode());
        $this->entityManager->persist($userBankAccount);
        $this->entityManager->flush();

        $this->entityManager->refresh($user);

        $this->eventDispatcher->dispatch(new UserBankAccountCreatedEvent($userBankAccount->getId()));

        $accessToken = $this->generateUserAccessToken($user, $requestDto->deviceUid);

        $response = new ApiDataResponse([
            'access_token' => $accessToken,
            'user' => $this->generateUserData($user)
        ], "Signup Successful!");

        return $this->json($response, Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] AuthLoginRequestDto $requestDto
    ): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['username' => $requestDto->username]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $requestDto->password)) {
            throw new InvalidAuthenticationException("Invalid Credentials!");
        }

        $this->accessTokenRepository->deleteByUser($user, $requestDto->deviceUid);

        $accessToken = $this->generateUserAccessToken($user, $requestDto->deviceUid);

        $response = new ApiDataResponse([
            'access_token' => $accessToken,
            'user' => $this->generateUserData($user),
        ], "Login Successful!");

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/forgot-password/initiate-otp', name: 'forgot_password_initiate', methods: ['POST'])]
    public function forgotPasswordInitiateOtp(
        #[MapRequestPayload] ForgotPasswordInitiateOtpRequestDto $requestDto
    ): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['username' => $requestDto->username]);
        if ($user) {
            $this->forgotPasswordTokenRepository->createQueryBuilder('forgot_password_token')
                ->delete()
                ->where('forgot_password_token.username = :username')
                ->setParameter('username', $requestDto->username)
                ->getQuery()
                ->execute();

            $forgotPasswordToken = (new ForgotPasswordToken())
                ->setUsername($user->getUsername())
                ->setOtpCode(RandomUtils::generateOtp())
                ->setExpiresInMinutes(15);
            $this->entityManager->persist($forgotPasswordToken);
            $this->entityManager->flush();

            // Send OTP Confirmation Email
            $this->eventDispatcher->dispatch(new SendForgotPasswordOtpEmailEvent($forgotPasswordToken->getId()));
        }

        $response = new ApiDataResponse([
            'message' => 'An OTP has been sent to your email, if it exists with us.'
        ]);
        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/forgot-password/verify-otp', name: 'forgot_password_verify', methods: ['POST'])]
    public function forgotPasswordVerifyOtp(
        #[MapRequestPayload] ForgotPasswordVerifyOtpRequestDto $requestDto
    ): JsonResponse
    {
        $forgotPasswordToken = $this->forgotPasswordTokenRepository->findOneBy([
            'username' => $requestDto->username,
            'otpCode' => $requestDto->otp
        ]);
        if (!$forgotPasswordToken || DateTimeUtils::hasDateTimeElapsedInMinutes($forgotPasswordToken->getCreatedAt(), $forgotPasswordToken->getExpiresInMinutes())) {
            throw new BadRequestException("OTP Request is invalid or has expired!");
        }

        $response = new ApiDataResponse([
            'message' => 'OTP verified successfully!'
        ]);
        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/forgot-password/finalize', name: 'forgot_password_finalize', methods: ['POST'])]
    public function forgotPasswordFinalize(
        #[MapRequestPayload] ForgotPasswordFinalizeRequestDto $requestDto
    ): JsonResponse
    {
        $forgotPasswordToken = $this->forgotPasswordTokenRepository->findOneBy(['username' => $requestDto->username]);
        if (!$forgotPasswordToken || DateTimeUtils::hasDateTimeElapsedInMinutes($forgotPasswordToken->getCreatedAt(), $forgotPasswordToken->getExpiresInMinutes())) {
            throw new BadRequestException("OTP Request is invalid or has expired!");
        }

        $user = $this->userRepository->findOneBy(['username' => $requestDto->username]);
        if (!$user) {
            throw new BadRequestException("User not found!");
        }

        if ($this->passwordHasher->isPasswordValid($user, $requestDto->password)) {
            throw new BadRequestException("Your new password is the same as your current password!");
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $requestDto->password));
        $this->entityManager->persist($user);

        $this->entityManager->remove($forgotPasswordToken);

        $this->entityManager->flush();

        // Send Password Changed email
        $this->eventDispatcher->dispatch(new SendForgotPasswordSuccessEmailEvent($user->getUsername()));

        $response = new ApiDataResponse([
            'message' => 'OTP verified successfully!'
        ]);
        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/access-token', name: 'access_token', methods: ['POST'])]
    public function accessToken(
        #[CurrentUser] User $currentUser,
        #[MapRequestPayload] AuthAccessTokenRequestDto $requestDto
    ): JsonResponse
    {
        $this->accessTokenRepository->deleteByUser($currentUser, $requestDto->deviceUid);

        $accessToken = $this->generateUserAccessToken($currentUser, $requestDto->deviceUid);

        $response = new ApiDataResponse([
            'access_token' => $accessToken,
            'user' => $this->generateUserData($currentUser),
        ], "Login Successful!");

        return $this->json($response, Response::HTTP_OK);
    }

    private function generateUserAccessToken(User $user, string $deviceUid): string
    {
        $accessToken = UidUtils::generateUid();
        $userAccessToken = (new UserAccessToken())
            ->setAccessToken($accessToken)
            ->setDeviceUid($deviceUid)
            ->setUser($user)
            ->setExpiresAt(new \DateTimeImmutable('+1 day'));
        $this->entityManager->persist($userAccessToken);
        $this->entityManager->flush();

        return $accessToken;
    }

    private function generateUserData(User $user): array
    {
        $wallet = $user->getWallet();
        $hasWalletTransactionPin = $wallet !== null && $wallet->getTransactionPin() !== null;
        return [
            'uid' => $user->getUid(),
            'username' => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone_number' => $user->getPhoneNumber(),
            'has_wallet_transaction_pin' => $hasWalletTransactionPin,
        ];
    }
}
