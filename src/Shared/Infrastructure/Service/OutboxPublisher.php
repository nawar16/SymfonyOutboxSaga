<?php

namespace App\Shared\Infrastructure\Service;

use App\Shared\Infrastructure\Persistence\DoctrineOutboxRepository;
use Symfony\Component\Messenger\MessageBusInterface;

final class OutboxPublisher
{
    public function __construct(
        private DoctrineOutboxRepository $outboxRepository,
        private MessageBusInterface $bus,
        private DomainEventDeserializer $deserializer
    ) 
    {}
    public function publishPending(int $limit = 100): void
    {
        $messages = $this->outboxRepository->findPending($limit);
        foreach ($messages as $message) {
            try {
                $event = $this->deserializer->deserialize(
                    $message->getEventType(),
                    $message->getPayload()
                );
                $this->bus->dispatch($event);
                $message->markAsSent();
                $this->outboxRepository->save($message);
            } catch (\Throwable $exception) {
                // TODO: here i should add a retry mechanism
                throw $exception;
            }
        }
    }
}