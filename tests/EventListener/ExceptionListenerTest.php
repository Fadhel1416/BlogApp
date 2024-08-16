<?php

declare(strict_types=1);

namespace App\Tests\EventListener;

use App\EventListener\ExceptionListener;
use App\Exception\ForbiddenException;
use App\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * tester l'exception RequestException
 */
class ExceptionListenerTest extends TestCase
{
    /**
     * declancher l'événement de l'exception
     *
     * @return void
     */
    public function testOnKernelException()
    {
        // Initialisation.
        $exceptionListener = new ExceptionListener();
        $httpKernelInterface = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $statutcode = 400;
        $exception = new RequestException();
        $event = new ExceptionEvent($httpKernelInterface, $request, 1, $exception);

        // Test réponse vide.
        $this->assertEmpty($event->getResponse());
        // Exécution de la méthode.
        $exceptionListener->onKernelException($event);
        $exceptionListener->getSubscribedEvents();
     
        // Test réponse avec bon code.
        $this->assertEquals($statutcode, $event->getResponse()->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class,$event->getResponse());
        $exception = new ForbiddenException();
        $event = new ExceptionEvent($httpKernelInterface, $request, 1, $exception);
        $exceptionListener->onKernelException($event);
        $exceptionListener->getSubscribedEvents();
        $this->assertEquals(401, $event->getResponse()->getStatusCode());

    }
}

