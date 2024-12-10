<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientTraceController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    )
    {
    }

    #[Route('/http-client-trace')]
    public function index(): Response
    {
        $this->httpClient->request('GET', 'http://grafana:3000/api/health');

        return new Response();
    }
}
