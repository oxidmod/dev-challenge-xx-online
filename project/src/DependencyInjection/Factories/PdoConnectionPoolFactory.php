<?php
declare(strict_types=1);

namespace App\DependencyInjection\Factories;

use App\Infrastructure\PgSql\PdoConnectionPool;
use OpenSwoole\Core\Coroutine\Client\PDOClientFactory;
use OpenSwoole\Core\Coroutine\Client\PDOConfig;
use PDO;

class PdoConnectionPoolFactory
{
    public function __construct(
        private readonly string $driver,
        private readonly string $host,
        private readonly int $port,
        private readonly string $name,
        private readonly string $user,
        private readonly string $pass,
    ) {
    }

    public function create(
        ?string $driver = null,
        ?string $host = null,
        ?int $port = null,
        ?string $name = null,
        ?string $user = null,
        ?string $pass = null,
    ): PdoConnectionPool {
        $config = (new PDOConfig())
            ->withDriver($driver ?? $this->driver)
            ->withHost($host ?? $this->host)
            ->withPort($port ?? $this->port)
            ->withDbname($name ?? $this->name)
            ->withUsername($user ?? $this->user)
            ->withPassword($pass ?? $this->pass)
            ->withCharset('utf8');

        return new PdoConnectionPool(PDOClientFactory::class, $config);
    }

    public function createPdo(
        ?string $driver = null,
        ?string $host = null,
        ?int $port = null,
        ?string $name = null,
        ?string $user = null,
        ?string $pass = null,
    ): PDO {
        $driver = $driver ?? $this->driver;
        $host = $host ?? $this->host;
        $port = $port ?? $this->port;
        $name = $name ?? $this->name;
        $user = $user ?? $this->user;
        $pass = $pass ?? $this->pass;

        return new PDO(
            "$driver:host=$host;port=$port;dbname=$name",
            $user,
            $pass,
        );
    }
}
