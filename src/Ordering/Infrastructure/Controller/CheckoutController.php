<?php

namespace App\Ordering\Infrastructure\Controller;

use App\Ordering\Application\Command\PlaceOrderCommand;
use App\Ordering\Application\Handler\PlaceOrderHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController
{
    public function __construct(private PlaceOrderHandler $handler
    ) {}

    #[Route('/api/checkout', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $command = new PlaceOrderCommand(
            $data['customerId'],
            $data['items']
        );
        $orderId = ($this->handler)($command);
        return new JsonResponse(['orderId' => $orderId]);
    }
}