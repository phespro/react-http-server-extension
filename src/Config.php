<?php

namespace Phespro\ReactHttpServerExtension;

class Config
{
    public function __construct(
        public string $host,
        public int $workerAmount,
        public array $socketServerConfiguration = [],
    )
    {
    }
}