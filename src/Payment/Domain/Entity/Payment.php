<?php

namespace App\Payment\Domain\Entity;

use App\Payment\Domain\Enum\PaymentStatus;
use App\Payment\Domain\Event\PaymentFailed;
use App\Payment\Domain\Event\PaymentSucceeded;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use InvalidArgumentException;

#[ORM\Entity]
#[ORM\Table(name: 'payments')]
final class Payment
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;
    #[ORM\Column(type: 'string', length: 36)]
    private string $orderId;
    #[ORM\Column(type: 'integer')]
    private int $amount;
    #[ORM\Column(enumType: PaymentStatus::class)]
    private PaymentStatus $status;
    /** @var array<object> */
    private array $domainEvents = [];

    private function __construct(string $id,string $orderId,int $amount) 
    {
        $amount <= 0 ? throw new InvalidArgumentException('Payment amount must be greater than zero'):'';
        $this->id = $id;
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->status = PaymentStatus::Pending;
    }

    public static function create(string $id,string $orderId,int $amount): self 
    {
        return new self($id, $orderId, $amount);
    }
    public function markSucceeded(): void
    {
        $this->status === PaymentStatus::Succeeded ? throw new DomainException('Payment is already succeeded'):'';
        $this->status = PaymentStatus::Succeeded;
        $this->recordEvent(new PaymentSucceeded($this->orderId,$this->amount));
    }
    public function markFailed(string $reason): void
    {
        $this->status === PaymentStatus::Failed ? throw new DomainException('Payment is already failed'):'';
        $this->status = PaymentStatus::Failed;
        $this->recordEvent(new PaymentFailed($this->orderId,$reason));
    }
    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }
    /** @return array<object> */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
    public function getId(): string{return $this->id;}
    public function getOrderId(): string{return $this->orderId;}
    public function getAmount(): int{return $this->amount;}
    public function getStatus(): PaymentStatus{return $this->status;}
}