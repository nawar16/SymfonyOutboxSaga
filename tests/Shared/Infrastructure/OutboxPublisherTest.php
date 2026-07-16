<?php

namespace App\Tests\Shared\Infrastructure;

use App\Shared\Infrastructure\Persistence\OutboxMessage;
use App\Shared\Infrastructure\Service\OutboxPublisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OutboxPublisherTest extends KernelTestCase
{
    public function testPublisherMarksMessagesAsSent(): void
    {
        self::bootKernel(); 
        $container = static::getContainer();
        $publisher = $container->get(OutboxPublisher::class);
        $entity_manager = $container->get(EntityManagerInterface::class);
        $publisher->publishPending();
        $messages = $entity_manager->getRepository(OutboxMessage::class)->findAll();
        $this->assertNotNull($messages[0]->getSentAt());
    }

}