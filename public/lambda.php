<?php
declare(strict_types=1);
require dirname(__DIR__).'/config/bootstrap.php';

use App\Kernel;
use Bref\Http\LambdaResponse;
use Bref\Http\LambdaRequest;

lambda(function (array $event) {
    $kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
    $request = LambdaRequest::create($event)->getSymfonyRequest();
    $response = $kernel->handle($request);

    try {
        return LambdaResponse::fromSymfonyResponse($response)
            ->toApiGatewayFormat(array_key_exists('multiValueHeaders', $event));
    } finally {
        $kernel->terminate($request, $response);
    }
});
