<?php

namespace App\Inventory\Infrastructure\Persistence;

use App\Inventory\Domain\Entity\InventoryReservation;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineInventoryReservationRepository
{
    public function __construct(private EntityManagerInterface $entityManager) 
    {}
    /**
     * @return InventoryReservation[]
     */
    public function findByOrderId(string $orderId): array
    {
        return $this->entityManager->createQueryBuilder()->select('r')
            ->from(InventoryReservation::class,'r')->where('r.orderId = :orderId')
            ->setParameter('orderId',$orderId)->getQuery()->getResult();
    }
    public function save(InventoryReservation $reservation): void 
    {
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
    }
}