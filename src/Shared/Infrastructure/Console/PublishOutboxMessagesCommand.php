<?php

namespace App\Shared\Infrastructure\Console;

use App\Shared\Infrastructure\Service\OutboxPublisher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:publish-outbox'
)]
final class PublishOutboxMessagesCommand extends Command
{
    public function __construct(private OutboxPublisher $publisher) 
    {
        parent::__construct();
    }
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int 
    {
        $this->publisher->publishPending();
        return Command::SUCCESS;
    }
}