<?php
declare(strict_types=1);

namespace App;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Runtime;
use Swoole\Server;
use Swoole\Server\Task;
use Throwable;

class Application
{
    public function __construct(
        private readonly Server $httpServer,
        private readonly LoggerInterface $logger,
        private readonly MinesDetector\Detector $minesDetector,
        private readonly string $appName,
    ) {
        Runtime::enableCoroutine();

        $this->httpServer->on('start', $this->bind('onStart'));
        $this->httpServer->on('workerStart', $this->bind('onWorkerStart'));
        $this->httpServer->on('task', $this->bind('onTask'));
        $this->httpServer->on('request', $this->bind('onRequest'));
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

    private function onTask(Server $server, Task $task): void
    {
        $this->logger->debug('[Application] Task received.', [
            'task' => $task->data,
            'task_id' => $task->id,
            'worker_id' => $task->worker_id,
        ]);
    }

    private function onRequest(Request $request, Response $response): void
    {
        $this->logger->debug('[Application] Request received', ['request' => $request]);

        match ($request->server['request_uri']) {
            '/api' => $this->handleIndex($request, $response),
            '/api/image-input' => $this->handleImageInput($request, $response),
            default => $this->handleNotFoundError($request, $response),
        };
    }

    private function handleIndex(Request $request, Response $response): void
    {
        $response->setHeader('Content-Type', 'application/json');
        $response->end(json_encode([
            'app' => $this->appName,
        ]));
    }

    private function handleImageInput(Request $request, Response $response): void
    {
        try {
            if ($request->getMethod() !== 'POST') {
                throw new RuntimeException('Method Not Allowed', 405);
            }

            $content = json_decode($request->getContent(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Bad request', 400);
            }

            $imageData = $content['image'] ?? '';
            $minLevel = $content['min_level'] ?? 0;

            $statusCode = 200;
            $body = [
                'mines' => $this->minesDetector->detectMines($imageData, $minLevel),
            ];
        } catch (InvalidArgumentException $exception) {
            $statusCode = 422;
            $body = [
                'error' => $exception->getMessage(),
            ];
        } catch (Throwable $exception) {
            $statusCode = $exception->getCode() ?: 500;
            $body = [
                'error' => $exception->getMessage(),
            ];
        } finally {
            $response->setStatusCode($statusCode);
            $response->setHeader('Content-Type', 'application/json');
            $response->end(json_encode($body));
        }
    }

    private function handleNotFoundError(Request $request, Response $response): void
    {
        $response->setStatusCode(404);
        $response->end();
    }

    private function bind(string $method): callable
    {
        return function () use ($method) {
            $this->$method(...func_get_args());
        };
    }
}
