<?php

namespace App\Inventory\Application\Handler;

use App\Inventory\Domain\Entity\InventoryReservation;
use App\Inventory\Domain\Event\InventoryReserved;
use App\Inventory\Domain\Exception\InventoryItemNotFoundException;
use App\Inventory\Domain\Repository\InventoryRepositoryInterface;
use App\Inventory\Domain\Repository\InventoryReservationRepositoryInterface;
use App\Ordering\Domain\Event\OrderPlaced;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final class ReserveInventoryHandler
{
    public function __construct(private InventoryRepositoryInterface $repository,
        private InventoryReservationRepositoryInterface $reservationRepository,private MessageBusInterface $bus) 
    {}
    public function __invoke(OrderPlaced $event): void
    {
        $reservations = [];
        foreach ($event->getItems() as $item) {
            $inventoryItem =$this->repository->findByProductId($item['productId']);
            if ($inventoryItem === null) throw new InventoryItemNotFoundException($item['productId']);
            $inventoryItem->reserve($item['quantity']);
            $reservation = new InventoryReservation(
                Uuid::v4()->toRfc4122(),$event->getOrderId(),
                $item['productId'],$item['quantity']);
            $this->repository->save($inventoryItem);
            $this->reservationRepository->save($reservation);
            $reservations[] = ['productId' => $item['productId'],'quantity' => $item['quantity']];
        }
        $this->bus->dispatch(new InventoryReserved($event->getOrderId(),$reservations));
    }
}