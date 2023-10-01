<?php
declare(strict_types=1);

namespace App\DependencyInjection\Factories;

use FastRoute\Dispatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function FastRoute\cachedDispatcher;

class RouterFactory
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly string $projectRootPath,
        private readonly bool $isDebugEnabled
    ) {
    }

    public function create(): Dispatcher
    {
        $routesClosure = require_once implode(DIRECTORY_SEPARATOR, [$this->projectRootPath, 'config', 'routes.php']);

        return cachedDispatcher(
            $routesClosure($this->container),
            [
                'cacheFile' => implode(DIRECTORY_SEPARATOR, [$this->projectRootPath, 'cache', 'route.cache']),
                'cacheDisabled' => !$this->isDebugEnabled,
            ]
        );
    }
}
