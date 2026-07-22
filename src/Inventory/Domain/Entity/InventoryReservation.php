<?php

namespace App\Inventory\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Inventory\Domain\Enum\ReservationStatus;

#[ORM\Entity]
#[ORM\Table(name: 'inventory_reservations')]
class InventoryReservation
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(type: 'string', length: 36)]
    private string $orderId;

    #[ORM\Column(type: 'string', length: 36)]
    private string $productId;

    #[ORM\Column(type: 'integer')]
    private int $quantity;
    #[ORM\Column(enumType: ReservationStatus::class)]
    private ReservationStatus $status;
    public function __construct(string $id,string $orderId,string $productId,int $quantity) 
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->status = ReservationStatus::Reserved;
    }
    public function confirm(): void
    {
        $this->status = ReservationStatus::Confirmed;
    }
    public function release(): void
    {
        $this->status = ReservationStatus::Released;
    }
    public function getOrderId(): string{return $this->orderId;}
    public function getProductId(): string{return $this->productId;}
    public function getQuantity(): int{return $this->quantity;}
    public function getStatus(): ReservationStatus{return $this->status;}
}