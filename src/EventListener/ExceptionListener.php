<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        #TODO Error handling
        $exception = $event->getThrowable();
        $response = new JsonResponse(['error' => $exception->getMessage()], 500);
        $event->setResponse($response);
    }
}