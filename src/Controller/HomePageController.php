<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface $logger,
    )
    {
    }

    #[Route('/')]
    public function homepage(): Response
    {
        $this->logger->info("[INFO] Log from HomePageController");

        return $this->render('home.html.twig', [
            'message' => 'Hello world!'
        ]);
    }
}
