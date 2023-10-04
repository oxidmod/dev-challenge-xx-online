<?php
declare(strict_types=1);

namespace App\DependencyInjection\Factories;

use FastRoute\Dispatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function FastRoute\cachedDispatcher;

readonly class RouterFactory
{
    public function __construct(
        private ContainerInterface $container,
        private string $projectRootPath,
    ) {
    }

    public function create(): Dispatcher
    {
        $routesClosure = require_once implode(DIRECTORY_SEPARATOR, [$this->projectRootPath, 'config', 'routes.php']);

        return cachedDispatcher(
            $routesClosure($this->container),
            [
                'cacheFile' => implode(DIRECTORY_SEPARATOR, [$this->projectRootPath, 'cache', 'route.cache']),
                'cacheDisabled' => true,
            ]
        );
    }
}
