<?php

namespace App\EventListener\Controller;

use App\Event\NewDebtCollectionEvent;
use App\Repository\DebtCollectionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Address;

class DebtCollectionControllerEventListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MessageBusInterface $messageBus,
        #[Autowire(param: 'sender_email_address')] private readonly string $senderEmailAddress,
        private readonly DebtCollectionRepository $debtCollectionRepository
    )
    {
    }

    #[AsEventListener(event: NewDebtCollectionEvent::class)]
    public function handleNewDebtCollectionRequestEvent(NewDebtCollectionEvent $event): void
    {
        $debtCollection = $this->debtCollectionRepository->findOneBy(['uid' => $event->debtCollectionUid]);
        if (!$debtCollection) {
            return;
        }

        $creator = $debtCollection->getCreatedBy();
        $participant = $debtCollection->getCreditor() === $creator ? $debtCollection->getDebtor() : $debtCollection->getCreditor();
        assert($participant !== $creator);
        $participantType = $participant === $debtCollection->getDebtor() ? 'Debtor' : 'Creditor';
        $creatorType = $creator === $debtCollection->getDebtor() ? 'Debtor' : 'Creditor';

        $recipient = new Address($participant->getUsername());
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmailAddress, "Dayn"))
            ->to($recipient)
            ->subject('New Debt Request')
            ->htmlTemplate('emails/debt_collection_new_request.html.twig')
            ->context([
                'fullName' => $participant->getFullName(),
                'creatorName' => $creator->getFullName(),
                'creatorType' => $creatorType,
                'amount' => $debtCollection->getAmount(),
                'debtDescription' => $debtCollection->getDescription(),
            ]);
        try {
            $this->messageBus->dispatch(new SendEmailMessage(message: $email));
        } catch (ExceptionInterface $e) {
            $this->logger->error("An error occurred while attempting to send a mail!");
            $this->logger->error($e->getMessage());
        }
    }
}