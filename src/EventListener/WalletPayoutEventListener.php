<?php

namespace App\EventListener;

use App\Entity\WalletPayout;
use App\Enum\WalletPayoutStatus;
use App\Event\WalletPayoutInitiatedEvent;
use App\Model\Integration\Paystack\PaystackInitiateTransferRequestDto;
use App\Repository\UserWalletRepository;
use App\Repository\WalletPayoutRepository;
use App\Service\PaystackService;
use App\Service\WalletPayoutService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class WalletPayoutEventListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly WalletPayoutRepository $walletPayoutRepository,
        private readonly LoggerInterface $logger,
        private readonly WalletPayoutService $walletPayoutService,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            WalletPayoutInitiatedEvent::class => 'onWalletPayoutInitiated',
        ];
    }

    public function onWalletPayoutInitiated(WalletPayoutInitiatedEvent $event): void
    {
        $walletPayout = $this->walletPayoutRepository->findOneBy(['id' => $event->getWalletPayoutId()]);
        if (!$walletPayout) {
            $this->logger->error("Wallet Payout with ID: {walletPayoutId} not found}", [
                'walletPayoutId' => $event->getWalletPayoutId(),
            ]);
            return;
        }

        $this->walletPayoutService->processQueuedWalletPayout($walletPayout);
    }
}