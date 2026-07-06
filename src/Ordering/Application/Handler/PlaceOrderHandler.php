<?php

namespace App\Ordering\Application\Handler;

use App\Ordering\Application\Command\PlaceOrderCommand;
use App\Ordering\Domain\Entity\Order;
use App\Ordering\Domain\Entity\OrderItem;
use App\Ordering\Domain\Repository\OrderRepositoryInterface;
use App\Ordering\Domain\Service\PriceLookupServiceInterface; 
use Symfony\Component\Uid\Uuid;

final class PlaceOrderHandler
{
    private OrderRepositoryInterface $orderRepo;
    private PriceLookupServiceInterface $priceLookupService;
    public function __construct(
        OrderRepositoryInterface $orderRepo,
        PriceLookupServiceInterface $priceLookupService
    ) {
        $this->orderRepo = $orderRepo;
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
        $order = Order::place($orderId, $customerId, $domainItems);
        //TODO: outbox listener 
        $this->orderRepo->save($order);
        return $orderId;
    }
}
