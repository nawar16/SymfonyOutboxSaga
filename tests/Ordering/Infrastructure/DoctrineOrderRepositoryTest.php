<?php

namespace App\Tests\Ordering\Infrastructure;

use App\Ordering\Domain\Entity\Order;
use App\Ordering\Domain\Entity\OrderItem;
use App\Ordering\Domain\Repository\OrderRepositoryInterface;
use App\Shared\Infrastructure\Persistence\OutboxMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineOrderRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        $entity_manager = static::getContainer()->get(EntityManagerInterface::class);
        $entity_manager->createQuery('DELETE FROM App\Shared\Infrastructure\Persistence\OutboxMessage')->execute();
        $entity_manager->createQuery('DELETE FROM App\Ordering\Domain\Entity\OrderItem')->execute();
        $entity_manager->createQuery('DELETE FROM App\Ordering\Domain\Entity\Order')->execute();
    }
    public function testSavingOrderAlsoPersistsOutboxMessage(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repository = $container->get(OrderRepositoryInterface::class);
        $entityManager = $container->get(EntityManagerInterface::class);
        $order = Order::place('order-1','customer-1',
            [new OrderItem('item-1','product-1',2,1000)]
        );
        $repository->save($order);
        $messages = $entityManager->getRepository(OutboxMessage::class)->findAll();
        $this->assertCount(1, $messages);
        $this->assertNotNull($entityManager->find(Order::class, 'order-1'));
        $this->assertCount(1,$entityManager->getRepository(OutboxMessage::class)->findAll());
    }
}