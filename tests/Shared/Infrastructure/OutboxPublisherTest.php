<?php

namespace App\Tests\Shared\Infrastructure\Service;

use App\Ordering\Domain\Event\OrderPlaced;
use App\Shared\Infrastructure\Persistence\DoctrineOutboxRepository;
use App\Shared\Infrastructure\Persistence\OutboxMessage;
use App\Shared\Infrastructure\Service\DomainEventDeserializer;
use App\Shared\Infrastructure\Service\OutboxPublisher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OutboxPublisherTest extends TestCase
{
    public function testPublishPendingDispatchesAndMarksMessageAsSent(): void
    {
        $event = new OrderPlaced('order-1','customer-1',2500,
            [
                ['productId' => 'product-1','quantity' => 2,],
            ]
        );
        $message = OutboxMessage::fromDomainEvent($event);
        $repository = $this->createMock(DoctrineOutboxRepository::class);
        $repository->expects($this->once())->method('findPending')->with(100)->willReturn([$message]);
        $repository->expects($this->once())->method('save')->with($message);
        $deserializer = $this->createMock(DomainEventDeserializer::class);
        $deserializer->expects($this->once())->method('deserialize')->with(
                $message->getEventType(),$message->getPayload()
            )->willReturn($event);
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->expects($this->once())->method('dispatch')->with($event)->willReturn(new Envelope($event));
        $publisher = new OutboxPublisher($repository,$bus,$deserializer);
        $published = $publisher->publishPending();
        $this->assertSame(1, $published);
        $this->assertTrue($message->isSent());
    }

    public function testPublishPendingThrowsExceptionWhenDispatchFails(): void
    {
        $event = new OrderPlaced('order-1','customer-1',2500,
            [
                ['productId' => 'product-1','quantity' => 2,],
            ]
        );
        $message = OutboxMessage::fromDomainEvent($event);
        $repository = $this->createMock(DoctrineOutboxRepository::class);
        $repository->expects($this->once())->method('findPending')->willReturn([$message]);
        $repository->expects($this->never())->method('save');
        $deserializer = $this->createMock(DomainEventDeserializer::class);
        $deserializer->method('deserialize')->willReturn($event);
        $bus = $this->createMock(MessageBusInterface::class);
        $bus->method('dispatch')->willThrowException(new \RuntimeException('RabbitMQ down'));
        $publisher = new OutboxPublisher($repository,$bus,$deserializer);
        $this->expectException(\RuntimeException::class);
        try {
            $publisher->publishPending();
        } finally{
            $this->assertFalse($message->isSent());
        }
    }

    public function testPublishPendingReturnsZeroWhenThereAreNoMessages(): void
    {
        $repository = $this->createMock(DoctrineOutboxRepository::class);
        $repository->expects($this->once())->method('findPending')->willReturn([]);
        $repository->expects($this->never())->method('save');
        $deserializer = $this->createMock(DomainEventDeserializer::class);
        $bus = $this->createMock(MessageBusInterface::class);
        $publisher = new OutboxPublisher($repository,$bus,$deserializer);
        $this->assertSame(0, $publisher->publishPending());
    }
}