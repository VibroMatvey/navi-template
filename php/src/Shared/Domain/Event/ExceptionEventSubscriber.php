<?php

namespace App\Shared\Domain\Event;

use App\Shared\Domain\Exception\ApiPlatform\DomainApiPlatformException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

readonly class ExceptionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private string $appEnv
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof DomainApiPlatformException) {
            $responseContent = [
                'status' => $exception->getStatusCode(),
                'details' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'trace' => $exception->getTrace(),
            ];

            if ($this->appEnv === 'prod') {
                unset($responseContent['trace']);
                unset($responseContent['details']);
            }

            $event->setResponse(new JsonResponse($responseContent, $exception->getStatusCode()));
        }
    }
}