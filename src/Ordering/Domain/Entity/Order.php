<?php

namespace App\Ordering\Domain\Entity;

use App\Ordering\Domain\Enum\OrderStatus;
use App\Ordering\Domain\Event\OrderPlaced;
use App\Ordering\Domain\Exception\DuplicateOrderItemException;
use App\Ordering\Domain\Exception\EmptyOrderException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(type: 'string', length: 36)]
    private string $customerId;
    #[ORM\Column(enumType: OrderStatus::class)]
    private OrderStatus $status;
    /** @var Collection<int, OrderItem> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $items;
    #[ORM\Column(type: 'integer')]
    private int $totalAmount = 0;
    private array $domainEvents = [];
    
    private function __construct(string $id, string $customerId)
    {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->status = OrderStatus::Pending;
        $this->items = new ArrayCollection(); 
    }
    /** @param array<OrderItem> $items */
    public static function place(string $id, string $customerId, array $items): self
    {
        if (empty($items)) 
            throw new EmptyOrderException();
        $order = new self($id, $customerId);
        foreach ($items as $item) {
            $order->addItem($item);
        }
        $order->recordEvent(new OrderPlaced($id, $customerId, $order->getTotalAmount(),         
        array_map(
            fn(OrderItem $item) => [
                'productId' => $item->getProductId(),
                'quantity' => $item->getQuantity()
            ],
            $order->getItems()
        )));
        return $order;
    }

    private function addItem(OrderItem $item): void
    {
        foreach ($this->items as $existingItem) 
            if ($existingItem->getProductId() === $item->getProductId())
                throw new DuplicateOrderItemException($item->getProductId());
        $this->items->add($item);
        $item->assignToOrder($this); 
        $this->recalculateTotal();
    }
    private function recalculateTotal(): void
    {
        $total = 0;
        foreach ($this->items as $item) 
            $total += $item->getPriceInCents() * $item->getQuantity();
        $this->totalAmount = $total;
    }
    // private function checkIfFullyShipped(): void
    // {
    //     foreach ($this->items as $item) 
    //         if ($item->getStatus() !== 'SHIPPED') return;
    //     $this->status = OrderStatus::Shipped;
    // }

    public function getId(): string { return $this->id; }
    public function getCustomerId(): string { return $this->customerId; }
    public function getStatus(): OrderStatus { return $this->status; }
    public function getTotalAmount(): int { return $this->totalAmount; }
    /** @return array<OrderItem> */
    public function getItems(): array { return $this->items->toArray(); }
   
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
