<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\DemoMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class SendMessageController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/send-message')]
    public function index(): Response
    {
        $this->messageBus->dispatch(new DemoMessage('an awesome message'));
        return new Response();
    }
}
