<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ForbiddenException;
use App\Exception\RequestException;
use Doctrine\DBAL\Types\ConversionException;
use Error;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $code = 400;
        if ($exception instanceof ForbiddenException) {
            $code = 401;
        }
        if ($exception instanceof RequestException || $exception instanceof NotFoundHttpException || $exception instanceof ConversionException || $exception instanceof Exception || $exception instanceof Error) {
            $event->setResponse(
                new JsonResponse(
                    [
                        'error' => $exception->getMessage(),
                        'code' => $code,
                    ],
                   $code
                )
           );
        }
    }
}
