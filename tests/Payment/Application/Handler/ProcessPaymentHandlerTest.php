<?php

namespace App\Tests\Payment\Application\Handler;

use App\Inventory\Domain\Event\InventoryReserved;
use App\Ordering\Domain\Entity\Order;
use App\Ordering\Domain\Repository\OrderRepositoryInterface;
use App\Payment\Application\Handler\ProcessPaymentHandler;
use App\Payment\Domain\Event\PaymentFailed;
use App\Payment\Domain\Event\PaymentSucceeded;
use App\Payment\Domain\Repository\PaymentRepositoryInterface;
use App\Payment\Domain\Service\PaymentGatewayInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProcessPaymentHandlerTest extends TestCase
{
    public function testSuccessfulPayment(): void
    {
        $orderRepository =$this->createMock(OrderRepositoryInterface::class);
        $order = $this->createMock(Order::class);
        $order->method('getTotalAmount')->willReturn(5000);
        $orderRepository->method('find')->willReturn($order);
        $paymentRepository =$this->createMock(PaymentRepositoryInterface::class);
        $paymentRepository->expects($this->once())->method('save');
        $gateway =$this->createMock(PaymentGatewayInterface::class);
        $gateway->method('charge')->willReturn(true);
        $bus =$this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(PaymentSucceeded::class));
        $handler = new ProcessPaymentHandler($paymentRepository,$orderRepository,$gateway,$bus);
        $reservations = [['item_id' => 'item-1', 'quantity' => 2]];
        $handler(new InventoryReserved('order-1', $reservations));
    }
    public function testFailedPaymentDispatchesFailureEvent(): void
    {
        $orderRepository =$this->createMock(OrderRepositoryInterface::class);
        $order = $this->createMock(Order::class);
        $order->method('getTotalAmount')->willReturn(5000);
        $orderRepository->method('find')->willReturn($order);
        $paymentRepository =$this->createMock(PaymentRepositoryInterface::class);
        $paymentRepository->expects($this->once())->method('save');
        $gateway =$this->createMock(PaymentGatewayInterface::class);
        $gateway->method('charge')->willReturn(false);
        $bus =$this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(PaymentFailed::class));
        $handler = new ProcessPaymentHandler($paymentRepository,$orderRepository,$gateway,$bus);
        $reservations = [['item_id' => 'item-1', 'quantity' => 2]];
        $handler(new InventoryReserved('order-1', $reservations));
    }
}