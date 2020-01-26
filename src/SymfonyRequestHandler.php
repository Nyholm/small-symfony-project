<?php

declare(strict_types=1);

namespace App;

use Bref\Context\Context;
use Bref\Event\Handler;
use Bref\Event\Http\HttpRequestEvent;
use Bref\Event\Http\HttpResponse;
use Bref\Event\Http\Psr7RequestFactory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;

class SymfonyRequestHandler implements Handler
{
    /**
     * @var callable
     */
    private $application;

    /**
     * @var HttpFoundationFactory
     */
    private $httpFoundationFactory;

    /**
     * @var PsrHttpFactory
     */
    private $psrHttpFactory;

    public function __construct(callable $application)
    {
        $this->application = $application;
        $this->httpFoundationFactory = new HttpFoundationFactory();

        $psr17Factory = new Psr17Factory();
        $this->psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
    }

    public function handle($event, Context $context)
    {
        $httpEvent = new HttpRequestEvent($event);
        $request = $this->httpFoundationFactory->createRequest(Psr7RequestFactory::fromEvent($httpEvent));

        $callable = $this->application;
        $response = $callable($request, $context);

        $eventHttpResponse = HttpResponse::fromPsr7Response($this->psrHttpFactory->createResponse($response));

        return $eventHttpResponse->toApiGatewayFormat($httpEvent->hasMultiHeader());
    }
}