<?php

namespace App\Ordering\Domain\Entity;

use App\Ordering\Domain\Event\OrderPlaced;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Order
{
    private string $id;
    private string $customerId;
    private string $inventoryHoldId;
    private string $status;
    /** @var Collection<int, OrderItem> */
    private Collection $items;
    private int $totalAmount = 0;
    private array $domainEvents = [];

    private function __construct(string $id, string $inventoryHoldId, string $customerId)
    {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->inventoryHoldId = $inventoryHoldId;
        $this->status = 'PENDING';
        $this->items = new ArrayCollection(); 
    }
    /** @param array<OrderItem> $items */
    public static function place(string $id, string $customerId, string $inventoryHoldId, array $items): self
    {
        if (empty($items)) 
            throw new \DomainException('An order must contain at least one item');
        $order = new self($id, $customerId, $inventoryHoldId);
        foreach ($items as $item) {
            $order->addItem($item);
        }
        $order->recordEvent(new OrderPlaced($id, $customerId, $inventoryHoldId, $order->getTotalAmount()));
        return $order;
    }

    private function addItem(OrderItem $item): void
    {
        foreach ($this->items as $existingItem) 
            if ($existingItem->getId() === $item->getId()) 
                throw new \DomainException('Item already exists in this order');
        $this->items->add($item);
        $item->setOrder($this); 
        $this->recalculateTotal();
    }
    //aggregate root control
    public function shipItem(string $itemId): void
    {
        foreach ($this->items as $item) {
            if ($item->getId() === $itemId) {
                $item->shipInternal();
                $this->checkIfFullyShipped(); 
                return;
            }
        }
        throw new \DomainException('Item not found in this order');
    }
    private function recalculateTotal(): void
    {
        $total = 0;
        foreach ($this->items as $item) 
            $total += $item->getPriceInCents() * $item->getQuantity();
        $this->totalAmount = $total;
    }
    private function checkIfFullyShipped(): void
    {
        foreach ($this->items as $item) 
            if ($item->getStatus() !== 'SHIPPED') return;
        $this->status = 'SHIPPED';
    }

    public function getId(): string { return $this->id; }
    public function getCustomerId(): string { return $this->customerId; }
    public function getStatus(): string { return $this->status; }
    public function getTotalAmount(): int { return $this->totalAmount; }
    /** @return array<OrderItem> */
    public function getItems(): array { return $this->items->toArray(); }
    public function getInventoryHoldId(): string { return $this->inventoryHoldId; } // ADDED GETTER
   
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
}
