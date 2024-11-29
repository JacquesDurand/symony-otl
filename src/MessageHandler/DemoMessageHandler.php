<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\DemoMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DemoMessageHandler
{
    public function __construct(
        private LoggerInterface $logger,
    )
    {
    }

    public function __invoke(DemoMessage $demoMessage): void
    {
        $this->logger->info($demoMessage->message);
    }
}