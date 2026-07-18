<?php

namespace App\Inventory\Application\EventHandler;

use App\Inventory\Domain\Event\InventoryReserved;
use App\Inventory\Infrastructure\Persistence\DoctrineInventoryRepository;
use App\Ordering\Domain\Event\OrderPlaced;
use DomainException;
use Symfony\Component\Messenger\MessageBusInterface;

final class ReserveInventoryHandler
{
    public function __construct(
        private DoctrineInventoryRepository $repository,
        private MessageBusInterface $bus) {}

    public function __invoke(OrderPlaced $event): void
    {
        foreach ($event->getItems() as $item) {
            $inventoryItem = $this->repository->findByProductId($item['productId']);
            if ($inventoryItem === null) 
                throw new DomainException(sprintf('Inventory item "%s" does not exist',$item['productId']));
            $inventoryItem->reserve($event->getOrderId(),$item['quantity']);
            $this->repository->save($inventoryItem);
            foreach ($inventoryItem->pullDomainEvents() as $domainEvent) 
                $this->bus->dispatch($domainEvent);
        }
    }
}