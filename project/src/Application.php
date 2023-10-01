<?php
declare(strict_types=1);

namespace App;

use OpenSwoole\Core\Psr\Middleware\StackHandler;
use OpenSwoole\Http\Server;
use Psr\Log\LoggerInterface;

class Application
{
    public function __construct(
        private readonly Server $httpServer,
        private readonly StackHandler $handler,
        private readonly LoggerInterface $logger,
        private readonly string $appName,
    ) {
        $this->httpServer->on('start', $this->onStart(...));
        $this->httpServer->setHandler($this->handler);
    }

    public function run(): void
    {
        $this->httpServer->start();
    }

    private function onStart(Server $server): void
    {
        $this->logger->debug('[Application] Server started', [
            'host' => $server->host,
            'port' => $server->port,
        ] + $server->setting);
    }
}
