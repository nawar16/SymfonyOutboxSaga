<?php

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'outbox_messages')]
class OutboxMessage
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(type: 'string', length: 36)]
    private string $eventId;
    #[ORM\Column(type: 'string', length: 255)]
    private string $eventType;
    #[ORM\Column(type: 'json')]
    private array $payload;
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $occurredAt;
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    private function __construct(){}
    public static function fromDomainEvent(DomainEvent $event): self
    {
        $message = new self();
        $message->id = Uuid::v4()->toRfc4122();
        $message->eventId = $event->getEventId();
        $message->eventType = $event::class;
        $message->payload = $event->toPayload();
        $message->occurredAt = $event->getOccurredAt();
        $message->createdAt = new DateTimeImmutable();
        return $message;
    }
    public function markAsSent(): void{$this->sentAt = new DateTimeImmutable();}
    public function isSent(): bool{return $this->sentAt !== null;}
    public function getPayload(): array{return $this->payload;}
    public function getEventType(): string{return $this->eventType;}
    public function getEventId(): string{return $this->eventId;}
    public function getSentAt(): ?DateTimeImmutable{return $this->sentAt;}
    public function getOccurredAt(): DateTimeImmutable{return $this->occurredAt;}
}