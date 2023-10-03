<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require 'vendor/autoload.php';

ini_set('display_errors', 'stderr');

// convert php errors to exceptions
set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        // This error code not included in error_reporting
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});

$projectDir = __DIR__;

// load .env config
$dotenv = Dotenv::createImmutable($projectDir);
$variables = $dotenv->safeLoad();

$isDebug = ($variables['APP_DEBUG'] ?? null) === 'true';

$file = "$projectDir/cache/container.php";
$containerConfigCache = new ConfigCache($file, $isDebug);

if (!$containerConfigCache->isFresh()) {
    $containerBuilder = new ContainerBuilder();
    $containerBuilder->setParameter('app.project_dir', $projectDir);
    $containerBuilder->setParameter('app.config_dir', "$projectDir/config");

    $loader = new YamlFileLoader(
        $containerBuilder,
        new FileLocator("$projectDir/config")
    );
    $loader->load('services.yaml');

    $containerBuilder->compile();

    $dumper = new PhpDumper($containerBuilder);
    $containerConfigCache->write(
        $dumper->dump(['class' => 'CachedContainer']),
        $containerBuilder->getResources()
    );
}

require_once $file;

return new CachedContainer();
