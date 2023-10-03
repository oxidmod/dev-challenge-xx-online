<?php
declare(strict_types=1);

namespace App\DependencyInjection\Factories;

use App\Infrastructure\PgSql\PdoConnectionPool;
use OpenSwoole\Core\Coroutine\Client\PDOClientFactory;
use OpenSwoole\Core\Coroutine\Client\PDOConfig;

class PdoConnectionPoolFactory
{
    public function create(
        string $driver,
        string $host,
        int $port,
        string $name,
        string $user,
        string $pass,
    ): PdoConnectionPool {
        $config = (new PDOConfig())
            ->withDriver($driver)
            ->withHost($host)
            ->withPort($port)
            ->withDbname($name)
            ->withUsername($user)
            ->withPassword($pass)
            ->withCharset('utf8');

        return new PdoConnectionPool(PDOClientFactory::class, $config);
    }
}
