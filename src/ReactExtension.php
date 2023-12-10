<?php

namespace Phespro\ReactHttpServerExtension;

use Phespro\Phespro\Extensibility\AbstractExtension;
use Phespro\Phespro\Kernel;

class ReactExtension extends AbstractExtension
{
    function boot(Kernel $kernel): void
    {
        $kernel->add(
            ReactServerInterface::class,
            fn() => new ReactServer($kernel),
        );

        $kernel->add(Config::class, fn() => new Config(
            getenv('PHESPRO_REACT_HOST') ?: '0.0.0.0:80',
            getenv('PHESPRO_REACT_WORKER') ?: 1,
        ));

        $kernel->add(
            StartServerCommand::class,
            fn() => new StartServerCommand(
                $kernel->getObject(Config::class),
                $kernel->getObject(ReactServerInterface::class),
            ),
            ['cli_command'],
        );
    }
}