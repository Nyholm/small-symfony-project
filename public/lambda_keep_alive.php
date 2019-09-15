<?php

declare(strict_types=1);

use App\Kernel;
use Bref\Context\Context;
use Bref\Http\LambdaResponse;
use Bref\Http\LambdaRequest;

/**
 * Using this file will make sure we keep our application loaded on AWS lambda.
 *
 * Environment variables
 *  - BREF_MEMORY_MAX: 1000000000
 *  - BREF_LOOP_MAX: 10
 */

include_once dirname(__DIR__).'/config/bootstrap.php';

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

return function(LambdaRequest $request, Context $context) use ($kernel) {
    echo json_encode($request->getRawEvent());
    $sfRequest = $request->getSymfonyRequest();

    $kernel->reboot($kernel->getCacheDir());
    $response = $kernel->handle($sfRequest);
    try {
        return LambdaResponse::fromSymfonyResponse($response);
    } finally {
        $kernel->terminate($sfRequest, $response);
    }
};
