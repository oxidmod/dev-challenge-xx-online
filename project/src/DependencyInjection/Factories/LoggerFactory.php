<?php
declare(strict_types=1);

namespace App\DependencyInjection\Factories;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public function create(
        string $loggerName,
        string $streamPath,
        int $logLevel
    ): LoggerInterface {
        return (new Logger($loggerName))
            ->pushHandler(
                new StreamHandler($streamPath, $logLevel)
            );
    }
}
