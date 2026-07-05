<?php

namespace App\Ordering\Domain\Entity;

class OrderItem
{
    private string $id; 
    private string $productId;
    private int $quantity;
    private int $priceInCents;
    private string $status;
    private Order $order; //for doctrine mapping

    public function __construct(string $id, string $productId, int $quantity, int $priceInCents)
    {
        $this->id = $id;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->priceInCents = $priceInCents;
        $this->status = 'PENDING';
    }
    public function shipInternal(): void
    {
        if ($this->status === 'SHIPPED') 
            throw new \DomainException('Item is already shipped');
        $this->status = 'SHIPPED';
    }

    public function setOrder(Order $order): void { $this->order = $order;}

    public function getId(): string { return $this->id; }
    public function getProductId(): string { return $this->productId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getPriceInCents(): int { return $this->priceInCents;}
    public function getStatus(): string { return $this->status; }
}
