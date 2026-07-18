<?php

namespace App\Inventory\Infrastructure\Fixtures;

use App\Inventory\Domain\Entity\InventoryItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class InventoryFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [['product-1', 100],['product-2', 50],['product-3', 200],
            ['product-4', 10],
            ['product-5', 500],
        ];
        foreach ($products as [$productId, $quantity]) 
            $manager->persist(new InventoryItem($productId,$quantity));
        $manager->flush();
    }
}