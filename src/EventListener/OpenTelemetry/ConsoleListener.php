<?php

declare(strict_types=1);

namespace App\EventListener\OpenTelemetry;

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Context\Context;
use OpenTelemetry\SemConv\TraceAttributes;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final readonly class ConsoleListener
{
    private TracerInterface $tracer;

    public function __construct()
    {
        $provider = Globals::tracerProvider();
        $this->tracer = $provider->getTracer(
            'app.console',
            '0.1.0',
            TraceAttributes::SCHEMA_URL
        );
    }

    #[AsEventListener(ConsoleCommandEvent::class, priority: 1000)]
    public function startCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        $name = $command?->getName();
        $class = $command ? $command::class : null;

        $span = $this->tracer
            ->spanBuilder($name ?? 'unknown command')
            ->setAttribute(TraceAttributes::CODE_FUNCTION, 'run')
            ->setAttribute(TraceAttributes::CODE_NAMESPACE, $class)
            ->startSpan();

        Context::storage()->attach($span->storeInContext(Context::getCurrent()));
    }

    #[AsEventListener(ConsoleErrorEvent::class, priority: -1000)]
    public function recordException(ConsoleErrorEvent $event): void
    {
        $span = Span::getCurrent();
        $span->recordException($event->getError());
    }

    #[AsEventListener(ConsoleTerminateEvent::class, priority: -1000)]
    public function terminateCommand(ConsoleTerminateEvent $event): void
    {
        if (!$scope = Context::storage()->scope()) {
            return;
        }

        $scope->detach();
        $span = Span::fromContext($scope->context());
        $span->setAttribute('symfony.console.exit_code', $event->getExitCode());
        match ($event->getExitCode()) {
            Command::SUCCESS => $span->setStatus(StatusCode::STATUS_OK),
            default => $span->setStatus(StatusCode::STATUS_ERROR),
        };
        $span->end();
    }
}