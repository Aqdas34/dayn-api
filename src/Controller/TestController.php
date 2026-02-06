<?php

namespace App\Controller;

use App\Model\Request\TestPinVerifyHashRequestDto;
use App\Service\Clicker\Clicker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\SodiumPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @psalm-type PsalmData = array{
 *     id: string,
 *     name: string|null
 * }
 */
#[Route('/api/test', name: 'app_test_')]
class TestController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    )
    {
    }

    #[Route('/hash-pin/{pin}', name: 'hash_pin', methods: ['GET'])]
    public function hashPin(string $pin): JsonResponse
    {
        $passwordHasher = new SodiumPasswordHasher();
        $hashedPin = $passwordHasher->hash($pin);
        return new JsonResponse([
            'hashed_pin' => $hashedPin,
            'pin' => $pin,
        ]);
    }

    #[Route('/verify-pin', name: 'verify_pin', methods: ['POST'])]
    public function verifyPin(#[MapRequestPayload] TestPinVerifyHashRequestDto $requestDto): JsonResponse
    {
        $passwordHasher = new SodiumPasswordHasher();
        $isPinValid = $passwordHasher->verify($requestDto->hash, $requestDto->pin);
        return new JsonResponse([
            'hash' => $requestDto->hash,
            'pin' => $requestDto->pin,
            'is_pin_valid' => $isPinValid,
        ]);
    }

    #[Route('/capture', name: 'capture', methods: ['POST'])]
    public function capture()
    {

    }

    #[Route('/click', name: 'click', methods: ['GET'])]
    public function click(
        Clicker $clicker
    ): JsonResponse
    {
        $clicker->click();

        return $this->json([
            'message' => 'Button clicked successfully.'
        ], Response::HTTP_OK);
    }

//    private function passwordHasher(): PasswordHasherFactoryInterface
//    {
//        $factory = new PasswordHasherFactory([
//            'auto'
//        ]);
//    }
}