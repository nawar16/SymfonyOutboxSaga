<?php
namespace App\Ordering\Domain\Repository;

use App\Ordering\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function find(string $id): ?Order;
}