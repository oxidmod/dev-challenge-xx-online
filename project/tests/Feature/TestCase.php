<?php
declare(strict_types=1);

namespace App\Tests\Feature;

use App\DependencyInjection\Factories\PdoConnectionPoolFactory;
use App\Infrastructure\PgSql\ConnectionManager;
use App\Infrastructure\PgSql\PdoConnectionPool;
use App\Tests\TestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestCase extends BaseTestCase
{
    protected static ContainerInterface $container;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $containerBuilder = require __DIR__ . '/../../container.php';
        self::$container  = $containerBuilder('test');

        self::$container->get(ConnectionManager::class)->setConnection(
            self::$container->get(PdoConnectionPoolFactory::class)->createPdo(name: self::$container->getParameter('db.name') . '_test')
        );
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::$container = null;
    }
}
