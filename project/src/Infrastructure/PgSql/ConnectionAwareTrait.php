<?php
declare(strict_types=1);

namespace App\Infrastructure\PgSql;

use OpenSwoole\Core\Coroutine\Client\PDOClient;
use PDO;

/**
 * @property-read PDO|PDOClient $connection
 */
trait ConnectionAwareTrait
{
    protected PDOClient|PDO $connection;

    public function setConnection(PDOClient|PDO $connection): void
    {
        $this->connection = $connection;
    }

    public function resetConnection(): void
    {
        unset($this->connection);
    }
}
