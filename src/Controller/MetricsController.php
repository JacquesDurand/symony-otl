<?php

declare(strict_types=1);

namespace App\Controller;

use OpenTelemetry\API\Globals;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/metrics')]
class MetricsController extends AbstractController
{

    #[Route('/{count}', requirements: ['count' => '\d+'])]
    public function index(int $count): Response
    {
        /** @var MeterProvider $provider */
        $provider = Globals::meterProvider();
        $countMeter = $provider->getMeter('app')->createCounter('testCounter', 'integer', 'the number passed by the user');
        $countMeter->add($count);
        $provider->forceFlush();

        return new Response();
    }

    #[Route('/memory')]
    public function getMemoryUsage(): Response
    {
        /** @var MeterProvider $provider */
        $provider = Globals::meterProvider();
        $gaugeMeter = $provider->getMeter('app')->createGauge('MemoryGauge', 'MB', 'The memory used by the function');
        $before = memory_get_usage();

        $str = str_repeat('Hello', 10000);

        $after = memory_get_usage();
        $gaugeMeter->record(($after - $before) / 1024 / 1024);
        $provider->forceFlush();

        return new Response();
    }
}
