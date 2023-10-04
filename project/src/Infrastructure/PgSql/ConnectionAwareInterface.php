<?php
declare(strict_types=1);

namespace App\Infrastructure\PgSql;

use OpenSwoole\Core\Coroutine\Client\PDOClient;
use PDO;

interface ConnectionAwareInterface
{
    public function setConnection(PDOClient|PDO $connection): void;

    public function resetConnection(): void;
}
