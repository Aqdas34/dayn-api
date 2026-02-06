<?php

namespace App\EventListener\Controller;

use App\Event\SendForgotPasswordOtpEmailEvent;
use App\Event\SendForgotPasswordSuccessEmailEvent;
use App\Repository\ForgotPasswordTokenRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Address;

class AuthControllerEventListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly MessageBusInterface $messageBus,
        #[Autowire(param: 'sender_email_address')] private readonly string $senderEmailAddress,
        private readonly ForgotPasswordTokenRepository $forgotPasswordTokenRepository,
        private readonly UserRepository $userRepository,
    )
    {
    }

    #[AsEventListener(event: SendForgotPasswordOtpEmailEvent::class)]
    public function handleSendForgotPasswordOtpEmailEvent(SendForgotPasswordOtpEmailEvent $event): void
    {
        $forgotPasswordToken = $this->forgotPasswordTokenRepository->find($event->forgotPasswordTokenId);
        if (!$forgotPasswordToken) {
            return;
        }

        $user = $this->userRepository->findOneBy(['username' => $forgotPasswordToken->getUsername()]);
        if (!$user) {
            return;
        }

        $recipient = new Address($forgotPasswordToken->getUsername());
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmailAddress, "Dayn"))
            ->to($recipient)
            ->subject('Forgot Password OTP')
            ->htmlTemplate('emails/forgot_password_otp.html.twig')
            ->context([
                'fullName' => $user->getFullName(),
                'otpCode' => $forgotPasswordToken->getOtpCode(),
                'expiresInMinutes' => $forgotPasswordToken->getExpiresInMinutes(),
            ]);
        try {
            $this->messageBus->dispatch(new SendEmailMessage(message: $email));
        } catch (ExceptionInterface $e) {
            $this->logger->error("An error occurred while attempting to send a mail!");
            $this->logger->error($e->getMessage());
        }
    }

    #[AsEventListener(event: SendForgotPasswordSuccessEmailEvent::class)]
    public function handleSendForgotPasswordSuccessEmailEvent(SendForgotPasswordSuccessEmailEvent $event): void
    {
        $user = $this->userRepository->findOneBy(['username' => $event->email]);
        if (!$user) {
            return;
        }

        $recipient = new Address($user->getUsername());
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmailAddress, "Dayn"))
            ->to($recipient)
            ->subject('Password Changed Successfully')
            ->htmlTemplate('emails/forgot_password_success.html.twig')
            ->context([
                'fullName' => $user->getFullName(),
            ]);
        try {
            $this->messageBus->dispatch(new SendEmailMessage(message: $email));
        } catch (ExceptionInterface $e) {
            $this->logger->error("An error occurred while attempting to send a mail!");
            $this->logger->error($e->getMessage());
        }
    }
}