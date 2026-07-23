<?php

namespace App\Inventory\Application\Handler;

use App\Payment\Domain\Event\PaymentFailed;
use App\Inventory\Domain\Enum\ReservationStatus;
use App\Inventory\Infrastructure\Persistence\DoctrineInventoryReservationRepository;
use App\Inventory\Infrastructure\Persistence\DoctrineInventoryRepository;
use RuntimeException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
final class ReleaseInventoryHandler
{
    public function __construct(private DoctrineInventoryReservationRepository $reservationRepository,
        private DoctrineInventoryRepository $inventoryRepository
    ) 
    {}
    public function __invoke(PaymentFailed $event): void 
    {
        $reservations =$this->reservationRepository->findByOrderId($event->getOrderId());
        foreach ($reservations as $reservation) {
            if ($reservation->getStatus()!== ReservationStatus::Reserved) continue;
            $inventory =$this->inventoryRepository->findByProductId($reservation->getProductId());
            if (!$inventory) throw new RuntimeException('Inventory item missing');
            $inventory->releaseReservation($reservation->getQuantity());
            $reservation->release();
            $this->inventoryRepository->save($inventory);
            $this->reservationRepository->save($reservation);
        }
    }
}