<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerInterface;

$containerBuilder = require_once __DIR__ . '/container.php';
/** @var ContainerInterface $container */
$container = $containerBuilder();

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinx_migrations',
        'default_environment' => 'prod',
        'prod' => [
            'adapter' => $container->getParameter('db.driver'),
            'host' => $container->getParameter('db.host'),
            'port' => $container->getParameter('db.port'),
            'name' => $container->getParameter('db.name'),
            'user' => $container->getParameter('db.user'),
            'pass' => $container->getParameter('db.pass'),
            'charset' => 'utf8',
        ],
        'test' => [
            'adapter' => $container->getParameter('db.driver'),
            'host' => $container->getParameter('db.host'),
            'port' => $container->getParameter('db.port'),
            'name' => $container->getParameter('db.name') . '_test',
            'user' => $container->getParameter('db.user'),
            'pass' => $container->getParameter('db.pass'),
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
