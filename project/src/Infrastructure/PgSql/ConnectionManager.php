<?php
declare(strict_types=1);

namespace App\Infrastructure\PgSql;

use Closure;
use OpenSwoole\Core\Coroutine\Client\PDOClient;
use PDO;
use RuntimeException;
use Throwable;

class ConnectionManager
{
    /** @var ConnectionAwareInterface[] */
    private array $services;

    private PDOClient|PDO $currentConnection;

    private bool $newConnectionFetched = false;

    public function __construct(
        private readonly PdoConnectionPool $connectionPool,
        iterable $services
    ) {
        foreach ($services as $service) {
            if ($service instanceof ConnectionAwareInterface) {
                $this->services[] = $service;
            }
        }
    }

    public function setConnection(PDOClient|PDO $connection): void
    {
        $this->currentConnection = $connection;
        $this->newConnectionFetched = false;

        foreach ($this->services as $service) {
            $service->setConnection($this->currentConnection);
        }
    }

    public function resetConnection(): void
    {
        foreach ($this->services as $service) {
            $service->resetConnection();
        }

        unset($this->currentConnection);
    }

    /**
     * @param Closure $action
     * @return mixed
     * @throws Throwable
     */
    public function transactional(Closure $action): mixed
    {
        /** @var PDOClient|PDO $connection */
        $connection = $this->getConnection();
        $transactionStarted = false;

        try {
            $transactionStarted = $connection->beginTransaction();
            if (!$transactionStarted) {
                throw new RuntimeException('Transaction start failed');
            }

            $result = $action();

            if(!$connection->commit()) {
                throw new RuntimeException('Transaction commit failed');
            }

            return $result;
        } catch (Throwable $exception) {
            if ($transactionStarted) {
                $connection->rollBack();
            }

            throw $exception;
        } finally {
            if ($this->newConnectionFetched) $this->resetConnection();
        }
    }

    private function getConnection(): PDOClient
    {
        if (!isset($this->currentConnection)) {
            $this->newConnectionFetched = true;
            $this->setConnection($this->connectionPool->get());
        }

        return $this->currentConnection;
    }
}
