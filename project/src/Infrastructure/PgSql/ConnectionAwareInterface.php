<?php
declare(strict_types=1);

namespace App\Infrastructure\PgSql;

use OpenSwoole\Core\Coroutine\Client\PDOClient;

interface ConnectionAwareInterface
{
    public function setConnection(PDOClient $connection): void;

    public function resetConnection(): void;
}
