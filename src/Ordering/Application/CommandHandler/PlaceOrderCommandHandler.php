<?php

namespace App\Ordering\Application\CommandHandler;

use App\Ordering\Application\Command\PlaceOrderCommand;
use App\Ordering\Domain\Entity\Order;
use App\Ordering\Domain\Entity\OrderItem;
use App\Ordering\Domain\Service\PriceLookupServiceInterface; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class PlaceOrderCommandHandler
{
    private EntityManagerInterface $entityManager;
    private PriceLookupServiceInterface $priceLookupService;
    public function __construct(
        EntityManagerInterface $entityManager,
        PriceLookupServiceInterface $priceLookupService
    ) {
        $this->entityManager = $entityManager;
        $this->priceLookupService = $priceLookupService;
    }

    public function __invoke(PlaceOrderCommand $command): string
    {
        $orderId = Uuid::v4()->toRfc4122(); 
        $customerId = $command->getCustomerId();
        $domainItems = [];
        foreach ($command->getItems() as $itemData) {
            $productId = $itemData['product_id'];
            $priceInCents = $this->priceLookupService->getLivePrice($productId);
            $itemId = Uuid::v4()->toRfc4122();
            $domainItems[] = new OrderItem(
                $itemId,
                $productId,
                $itemData['quantity'],
                $priceInCents
            );
        }
        //track the saga reservation
        $order = Order::place($orderId, $customerId, $command->getInventoryHoldId(), $domainItems);
        //TODO: outbox listener will intercept here
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        return $orderId;
    }
}
