<?php

namespace App\Ordering\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_items')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id; 
    #[ORM\Column(type: 'string', length: 36)]
    private string $productId;
    #[ORM\Column(type: 'integer')]
    private int $quantity;
    #[ORM\Column(type: 'integer')]
    private int $priceInCents;
    #[ORM\Column(type: 'string', length: 50)]
    private string $status;
    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
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
