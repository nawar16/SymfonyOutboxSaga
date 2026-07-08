<?php

namespace App\Tests\Ordering\Domain;

use App\Ordering\Domain\Entity\Order;
use App\Ordering\Domain\Entity\OrderItem;
use App\Ordering\Domain\Exception\EmptyOrderException;
use App\Ordering\Domain\Exception\InvalidQuantityException;
use PHPUnit\Framework\TestCase;

final class OrderTest extends TestCase
{
    public function testPlaceOrderCalculatesTotalAndRecordsEvent(): void
    {
        $items = 
        [
            new OrderItem('item-1', 'product-1', 2, 1000),
            new OrderItem('item-2', 'product-2', 1, 500),
        ];
        $order = Order::place('order-1', 'customer-1', $items);
        $this->assertSame(2500, $order->getTotalAmount());
        $events = $order->pullDomainEvents();
        $this->assertCount(1, $events);
    }
    public function testEmptyOrderException(): void
    {
        $this->expectException(EmptyOrderException::class);
        Order::place('order-1', 'customer-1',[]);
    }
    public function testInvalidQuantityException(): void
    {
        $this->expectException(InvalidQuantityException::class);
        new OrderItem('item-1','product-1',0,1000);
    }
}