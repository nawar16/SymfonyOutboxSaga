<?php

namespace App\Tests\Inventory\Application\EventHandler;

use App\Inventory\Application\Handler\ReserveInventoryHandler;
use App\Inventory\Domain\Entity\InventoryItem;
use App\Inventory\Domain\Repository\InventoryRepositoryInterface;
use App\Inventory\Infrastructure\Persistence\DoctrineInventoryRepository;
use App\Ordering\Domain\Event\OrderPlaced;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ReserveInventoryHandlerTest extends TestCase
{
    public function testHandlerReservesInventory(): void
    {
        $inventory = new InventoryItem('product-1',100);
        $repository = $this->createMock(InventoryRepositoryInterface::class);
        $repository->expects($this->once())->method('findByProductId')->with('product-1')->willReturn($inventory);
        $repository->expects($this->once())->method('save')->with($inventory);
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())->method('dispatch')->willReturnCallback(fn ($event) => new Envelope($event));
        $handler = new ReserveInventoryHandler($repository,$bus);
        $event = new OrderPlaced('order-1','customer-1',5000,[['productId' => 'product-1','quantity' => 10]]);
        $handler($event);
        $this->assertSame(90,$inventory->getAvailableQuantity());
        $this->assertSame(10,$inventory->getReservedQuantity());
    }
    public function testThrowsWhenInventoryDoesNotExist(): void
    {
        $repository = $this->createMock(InventoryRepositoryInterface::class);
        $repository->method('findByProductId')->willReturn(null);
        $bus = $this->createMock(MessageBusInterface::class);
        $handler = new ReserveInventoryHandler($repository,$bus);
        $event = new OrderPlaced('order-1','customer-1',5000,[['productId' => 'missing-product','quantity' => 5]]);
        $this->expectException(\DomainException::class);
        $handler($event);
    }
}