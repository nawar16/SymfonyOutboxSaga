<?php

namespace App\Payment\Application\Handler;

use App\Inventory\Domain\Event\InventoryReserved;
use App\Ordering\Infrastructure\Persistence\DoctrineOrderRepository;
use App\Payment\Domain\Entity\Payment;
use App\Payment\Domain\Exception\PaymentProcessingException;
use App\Payment\Domain\Service\PaymentGatewayInterface;
use App\Payment\Infrastructure\Persistence\DoctrinePaymentRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final class ProcessPaymentHandler
{
    public function __construct(
        private DoctrinePaymentRepository $repository,
        private DoctrineOrderRepository $orderRepository,
        private PaymentGatewayInterface $gateway,
        private MessageBusInterface $bus
    ) 
    {}
    public function __invoke(InventoryReserved $event): void
    {
        $order = $this->orderRepository->find($event->getOrderId());
        if (!$order) throw new PaymentProcessingException($event->getOrderId());
        $amount = $order->getTotalAmount();
        $paymentId = Uuid::v4()->toRfc4122();
        $payment = Payment::create($paymentId, $event->getOrderId(), $amount);
        $success = $this->gateway->charge($payment->getOrderId(), $payment->getAmount());
        if ($success) 
            $payment->markSucceeded();
        else 
            $payment->markFailed('Gateway transaction declined');
        $this->repository->save($payment);
        foreach ($payment->pullDomainEvents() as $domainEvent) 
            $this->bus->dispatch($domainEvent);
        if (!$success) 
            throw new PaymentProcessingException($payment->getOrderId());
    }
}
