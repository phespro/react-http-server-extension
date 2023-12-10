#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Laminas\Diactoros\Response;
use League\Route\Router;
use Phespro\Phespro\Extensibility\AbstractExtension;
use Phespro\Phespro\Kernel;
use Phespro\ReactHttpServerExtension\ReactExtension;

class TestExtension extends AbstractExtension
{
    function bootHttp(Kernel $kernel, Router $router): void
    {
        $router->get('/', function() {
            $response = new Response;
            $response->getBody()->write('Hello World');
            return $response;
        });
    }
}

$kernel = new Kernel([
    ReactExtension::class,
    TestExtension::class,
]);

$kernel->handleCli();