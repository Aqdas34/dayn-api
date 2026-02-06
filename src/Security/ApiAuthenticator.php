<?php

namespace App\Security;

use App\Exception\Api\InvalidAuthenticationException;
use App\Repository\UserAccessTokenRepository;
use App\Repository\UserRepository;
use App\Util\DateTimeUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly UserRepository $userRepository,
        private readonly UserAccessTokenRepository $accessTokenRepository,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        $bearerToken = $request->headers->get('Authorization') ?? '';
        $bearerToken = str_replace('Bearer ', '', $bearerToken);

        $userAccessToken = $this->accessTokenRepository->findOneBy(['accessToken' => $bearerToken]);
        if (!$userAccessToken || DateTimeUtils::hasDateTimeElapsed($userAccessToken->getExpiresAt())) {
            throw new CustomUserMessageAuthenticationException("Invalid Credentials!");
        }

        $user = $userAccessToken->getUser();

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier()),
        );
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $this->logger->info('Login Successful!');
        $this->logger->info("Token User Identifier: " . $token->getUserIdentifier());
        foreach ($token->getRoleNames() as $roleName) {
            $this->logger->info("Role: " . $roleName);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $status = Response::HTTP_UNAUTHORIZED;

        $internalError = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new JsonResponse([
            'status' => $status,
            'message' => $exception->getMessage(),
            'errors' => [$internalError],
        ], Response::HTTP_UNAUTHORIZED);
    }
}