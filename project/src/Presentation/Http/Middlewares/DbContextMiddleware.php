<?php
declare(strict_types=1);

namespace App\Presentation\Http\Middlewares;

use App\Infrastructure\PgSql\ConnectionManager;
use App\Infrastructure\PgSql\PdoConnectionPool;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class DbContextMiddleware implements MiddlewareInterface
{
    public function __construct(
        private PdoConnectionPool $connectionPool,
        private ConnectionManager $connectionManager,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $connection = $this->connectionPool->get();

            $this->connectionManager->setConnection($connection);

            return $handler->handle($request);
        } catch (PDOException $exception) {
            $connection = null;

            throw $exception;
        } finally {
            $this->connectionManager->resetConnection();
            $this->connectionPool->put($connection);
        }
    }
}
