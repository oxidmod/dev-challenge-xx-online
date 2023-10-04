<?php
declare(strict_types=1);

use App\Presentation\Http\Controllers\GetCellController;
use App\Presentation\Http\Controllers\GetSheetController;
use App\Presentation\Http\Controllers\IndexController;
use App\Presentation\Http\Controllers\SaveCellController;
use FastRoute\RouteCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

return function (ContainerInterface $container): Closure {
    return function (RouteCollector $r) use ($container) {
        $r->addGroup('/api/v1', function (RouteCollector $r) use ($container) {
            $r->get('', $container->get(IndexController::class));

            $r->get('/{sheetId}', $container->get(GetSheetController::class));
            $r->get('/{sheetId}/{cellId}', $container->get(GetCellController::class));
            $r->post('/{sheetId}/{cellId}', $container->get(SaveCellController::class));
        });
    };
};
