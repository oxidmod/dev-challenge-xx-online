<?php
declare(strict_types=1);

use App\Presentation\Http\Controllers\IndexController;
use App\Presentation\Http\Controllers\SaveSheetController;
use FastRoute\RouteCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

return function (ContainerInterface $container): Closure {
    return function (RouteCollector $r) use ($container) {
        $r->get('/', $container->get(IndexController::class));

        $r->addGroup('/api/v1', function (RouteCollector $r) use ($container) {
            $r->post('/{sheetId}/{cellId}', $container->get(SaveSheetController::class));
        });
    };
};
