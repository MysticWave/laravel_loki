<?php

namespace App\Support;

use Monolog\Formatter\JsonFormatter;

class LokiLogFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new JsonFormatter(
                1,
                true,
                false,
                true
            ));
        }
    }
}
