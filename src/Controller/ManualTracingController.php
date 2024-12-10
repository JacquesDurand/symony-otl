<?php

declare(strict_types=1);

namespace App\Controller;

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\SDK\Trace\Tracer;
use OpenTelemetry\SemConv\TraceAttributes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManualTracingController extends AbstractController
{
    private TracerInterface $tracer;

    public function __construct()
    {
        $this->tracer = Globals::tracerProvider()->getTracer('app', '0.1.0', TraceAttributes::SCHEMA_URL);
    }

    #[Route('/manual-tracing')]
    public function index(): Response
    {
        $span = $this->tracer->spanBuilder('manual-tracing')->startSpan();

        $span->setAttribute(TraceAttributes::CODE_FUNCTION, 'index')
            ->setAttribute(TraceAttributes::CODE_NAMESPACE, self::class);

        $this->doSpecificStuff();

        $span->end();
        return new Response();
    }

    private function doSpecificStuff(): void
    {
        $parent = Span::getCurrent();
        $scope = $parent->activate();
        $nestedSpan = $this->tracer->spanBuilder('doSpecificStuff-nested-span')
            ->startSpan()
            ->setAttribute(TraceAttributes::CODE_FUNCTION, 'doSpecificStuff')
            ->setAttribute(TraceAttributes::CODE_NAMESPACE, self::class)
        ;
        // do stuff
        $random = random_int(0, 10);
        $nestedSpan->setAttribute('app.random_number_value', $random);

        $nestedSpan->end();
        $scope->detach();

    }
}
