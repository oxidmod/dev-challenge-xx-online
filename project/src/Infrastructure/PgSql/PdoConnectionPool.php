<?php
declare(strict_types=1);

namespace App\Infrastructure\PgSql;

use OpenSwoole\Core\Coroutine\Client\PDOClient;
use OpenSwoole\Core\Coroutine\Pool\ClientPool;

/**
 * @method PDOClient get(float $timeout = -1)
 * @method void put(?PDOClient $connection, bool $isNew = false)
 */
class PdoConnectionPool extends ClientPool
{
}
