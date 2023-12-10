<?php

namespace Phespro\ReactHttpServerExtension;

use Psr\Log\LoggerInterface;

interface ReactServerInterface
{
    public function run(Config $config, LoggerInterface $logger, bool $isChild): void;
}