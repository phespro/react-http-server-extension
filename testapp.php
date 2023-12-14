#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Laminas\Diactoros\Response;
use League\Route\Router;
use Phespro\Phespro\Configuration\FrameworkConfiguration;
use Phespro\Phespro\Extensibility\AbstractExtension;
use Phespro\Phespro\Kernel;
use Phespro\ReactHttpServerExtension\ReactExtension;
use Psr\Container\ContainerInterface;

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

$kernel->decorate('config', function(ContainerInterface $c, FrameworkConfiguration $inner) {
    $inner->displayErrorDetails = true;
    return $inner;
});

$kernel->handleCli();