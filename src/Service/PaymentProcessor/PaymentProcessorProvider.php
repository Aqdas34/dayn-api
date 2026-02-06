<?php

namespace App\Service\PaymentProcessor;

use App\Model\Integration\Common\PaymentProvider;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\Service\ServiceCollectionInterface;

class PaymentProcessorProvider
{
    public function __construct(
        #[AutowireLocator(
            services: PaymentProcessorInterface::class,
        )]
        private readonly ServiceCollectionInterface $paymentProcessorServiceLocator
    )
    {
    }

    public function getProcessor(PaymentProvider $paymentProvider): ?PaymentProcessorInterface
    {
        if (!$this->paymentProcessorServiceLocator->has($paymentProvider->value)) {
            return null;
        }

        return $this->paymentProcessorServiceLocator->get($paymentProvider->value);
    }
}