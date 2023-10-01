<?php
declare(strict_types=1);

namespace App\Presentation\Http\Middlewares;

use App\Presentation\Http\Response;
use FastRoute\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Dispatcher $routeDispatcher
    ){
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeInfo = $this->routeDispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        return match ($routeInfo[0]) {
            Dispatcher::FOUND => $this->processRoute($request, $routeInfo),
            Dispatcher::NOT_FOUND => Response::notFound(),
            Dispatcher::METHOD_NOT_ALLOWED => Response::methodNotAllowed($routeInfo[1]),
            default => Response::serverError(),
        };
    }

    private function processRoute(ServerRequestInterface $request, array $routeInfo): ResponseInterface
    {
        foreach ($routeInfo[2] as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $routeInfo[1]($request);
    }
}
