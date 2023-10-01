<?php
declare(strict_types=1);

namespace App;

use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use OpenSwoole\Http\Server;
use Psr\Log\LoggerInterface;

class Application
{
    public function __construct(
        private readonly Server $httpServer,
        private readonly LoggerInterface $logger,
        private readonly string $appName,
    ) {
        $this->httpServer->on('start', $this->onStart(...));
        $this->httpServer->on('workerStart', $this->onWorkerStart(...));
        $this->httpServer->on('request', $this->onRequest(...));
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

    private function onWorkerStart(Server $server, int $workerId): void
    {
        $this->logger->debug('[Application] Worker started', ['worker_id' => $workerId]);
    }

    private function onRequest(Request $request, Response $response): void
    {
        $this->logger->debug('[Application] Request received', ['request' => $request]);
    }
}
