<?php
declare(strict_types=1);

namespace App\Infrastructure\PgSql;

use OpenSwoole\Core\Coroutine\Client\PDOClient;

/**
 * @property-read \PDO|PDOClient $connection
 */
trait ConnectionAwareTrait
{
    protected PDOClient $connection;

    public function setConnection(PDOClient $connection): void
    {
        $this->connection = $connection;
    }

    public function resetConnection(): void
    {
        unset($this->connection);
    }
}
