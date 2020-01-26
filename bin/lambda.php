<?php

declare(strict_types=1);

use App\Kernel;
use Bref\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use App\SymfonyRequestHandler;

/**
 * Using this file will make sure we keep our application loaded on AWS lambda.
 *
 * Environment variables
 *  - BREF_MEMORY_MAX: 1000000000
 *  - BREF_LOOP_MAX: 10
 */

include_once dirname(__DIR__).'/config/bootstrap.php';

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

return new SymfonyRequestHandler(function(Request $request, Context $context) use ($kernel) {

    //$kernel->reboot($kernel->getCacheDir());
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);

    return $response;
});
