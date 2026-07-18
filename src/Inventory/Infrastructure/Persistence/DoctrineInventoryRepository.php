<?php

namespace App\Inventory\Infrastructure\Persistence;

use App\Inventory\Domain\Entity\InventoryItem;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineInventoryRepository
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    public function findByProductId(string $productId): ?InventoryItem
    {
        return $this->entityManager->find(InventoryItem::class,$productId);
    }
    public function save(InventoryItem $item): void
    {
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }
}