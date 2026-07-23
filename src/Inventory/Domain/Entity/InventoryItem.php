<?php

namespace App\Inventory\Domain\Entity;

use App\Inventory\Domain\Event\InventoryReserved;
use App\Inventory\Domain\Exception\InsufficientStockException;
use App\Inventory\Domain\Exception\InvalidInventoryQuantityException;
use App\Inventory\Domain\Exception\InvalidReservationException;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'inventory_items')]
class InventoryItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $productId;
    #[ORM\Column(type: 'integer')]
    private int $availableQuantity;
    #[ORM\Column(type: 'integer')]
    private int $reservedQuantity = 0;
    /** @var array<object> */
    private array $domainEvents = [];

    public function __construct(string $productId,int $availableQuantity) 
    {
        trim($productId) === ''? throw new InvalidArgumentException('Product ID can\'t be empty'):'';
        $availableQuantity < 0? throw new InvalidArgumentException('Available quantity cannot be negative'):'';
        $this->productId = $productId;
        $this->availableQuantity = $availableQuantity;
    }

    public function reserve(int $quantity): void
    {
        $quantity <= 0? throw new InvalidInventoryQuantityException($quantity):'';
        $quantity > $this->availableQuantity? 
        throw new InsufficientStockException($this->productId,$quantity,$this->availableQuantity):'';
        $this->availableQuantity -= $quantity;
        $this->reservedQuantity += $quantity;
        //$this->recordEvent(new InventoryReserved($orderId,$this->productId,$quantity));
    }
    public function releaseReservation(int $quantity): void
    {
        $quantity <= 0? throw new InvalidInventoryQuantityException($quantity):'';
        $quantity > $this->reservedQuantity? throw new InvalidReservationException('Can\'t release more than the reserved quantity'):'';
        $this->reservedQuantity -= $quantity;
        $this->availableQuantity += $quantity;
    }
    public function confirmReservation(int $quantity): void
    {
        $quantity <= 0 ? throw new InvalidInventoryQuantityException($quantity):'';
        $quantity > $this->reservedQuantity ? throw new InvalidReservationException('Can\'t release more than the reserved quantity'):'';
        $this->reservedQuantity -= $quantity;
    }
    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
    /** @return array<object> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
    public function getProductId(): string
    {
        return $this->productId;
    }
    public function getAvailableQuantity(): int
    {
        return $this->availableQuantity;
    }
    public function getReservedQuantity(): int
    {
        return $this->reservedQuantity;
    }
}