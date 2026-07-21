<?php

namespace App\Tests\Payment\Application\Handler;

use App\Inventory\Domain\Event\InventoryReserved;
use App\Ordering\Domain\Entity\Order;
use App\Ordering\Domain\Repository\OrderRepositoryInterface;
use App\Payment\Application\Handler\ProcessPaymentHandler;
use App\Payment\Domain\Exception\PaymentProcessingException;
use App\Payment\Domain\Repository\PaymentRepositoryInterface;
use App\Payment\Domain\Service\PaymentGatewayInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProcessPaymentHandlerTest extends TestCase
{
    public function testSuccessfulPayment(): void
    {
        $repository = $this->createMock(PaymentRepositoryInterface::class);
        $repository->expects($this->once())->method('save');
        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getTotalAmount')->willReturn(5000);
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->method('find')->with('order-1')->willReturn($orderMock);  
        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $gateway->method('charge')->willReturn(true);
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())->method('dispatch')->willReturnCallback(fn ($message) => new Envelope($message));
        $handler = new ProcessPaymentHandler($repository, $orderRepository, $gateway, $bus);
        $event = new InventoryReserved('order-1', 'product-1', 2);
        $handler($event);
    }

    public function testFailedPaymentThrowsException(): void
    {
        $repository = $this->createMock(PaymentRepositoryInterface::class);
        $orderMock = $this->createMock(Order::class);
        $orderMock->method('getTotalAmount')->willReturn(5000);
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $orderRepository->method('find')->with('order-1')->willReturn($orderMock);
        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $gateway->method('charge')->willReturn(false);
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())->method('dispatch')->willReturnCallback(fn ($message) => new Envelope($message));
        $handler = new ProcessPaymentHandler($repository, $orderRepository, $gateway, $bus);
        $this->expectException(PaymentProcessingException::class);
        $handler(new InventoryReserved('order-1', 'product-1', 2));
    }
}
