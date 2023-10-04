<?php
declare(strict_types=1);

namespace App\Tests\Feature;

use App\DependencyInjection\Factories\PdoConnectionPoolFactory;
use App\Infrastructure\PgSql\ConnectionManager;
use App\Tests\TestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TestCase extends BaseTestCase
{
    protected static ?ContainerInterface $container = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $container = self::getContainer();
        $container->get(ConnectionManager::class)->setConnection(
            $container->get(PdoConnectionPoolFactory::class)->createPdo(name: $container->getParameter('db.name') . '_test')
        );
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        self::$container = null;
    }

    protected static function getContainer(): ContainerInterface
    {
        if (self::$container === null) {
            $containerBuilder = require __DIR__ . '/../../container.php';
            self::$container  = $containerBuilder('test');
        }

        return self::$container;
    }
}
