<?php

namespace App\Inventory\Application\Handler;

use App\Payment\Domain\Event\PaymentSucceeded;
use App\Inventory\Domain\Enum\ReservationStatus;
use App\Inventory\Infrastructure\Persistence\DoctrineInventoryReservationRepository;
use App\Inventory\Infrastructure\Persistence\DoctrineInventoryRepository;
use RuntimeException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
final class ConfirmInventoryHandler
{
    public function __construct(private DoctrineInventoryReservationRepository $reservationRepository,
        private DoctrineInventoryRepository $inventoryRepository
    ) 
    {}
    public function __invoke(PaymentSucceeded $event): void 
    {
        $reservations =$this->reservationRepository->findByOrderId($event->getOrderId());
        foreach ($reservations as $reservation) {
            if ($reservation->getStatus()!== ReservationStatus::Reserved) continue;
            $inventory =$this->inventoryRepository->findByProductId($reservation->getProductId());
            if (!$inventory) throw new RuntimeException('Inventory item missing');
            $inventory->confirmReservation($reservation->getQuantity());
            $reservation->confirm();
            $this->inventoryRepository->save($inventory);
            $this->reservationRepository->save($reservation);
        }
    }
}