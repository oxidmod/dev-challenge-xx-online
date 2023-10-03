<?php
declare(strict_types=1);

namespace App\Presentation\Http\Middlewares;

use App\Infrastructure\PgSql\ConnectionManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class TransactionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ConnectionManager $connectionManager,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return match (true) {
            $this->isWriteRequest($request) => $this->connectionManager->transactional(fn() => $handler->handle($request)),
            default => $handler->handle($request),
        };
    }

    private function isWriteRequest(ServerRequestInterface $request): bool
    {
        return in_array(
            strtoupper($request->getMethod()),
            ['DELETE', 'PATCH', 'POST', 'PUT']
        );
    }
}
