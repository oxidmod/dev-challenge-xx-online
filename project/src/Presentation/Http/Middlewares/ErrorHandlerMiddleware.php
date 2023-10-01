<?php
declare(strict_types=1);

namespace App\Presentation\Http\Middlewares;

use App\Presentation\Http\Dto\InvalidDtoException;
use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (InvalidDtoException $exception) {
            return Response::json($exception, 422);
        } catch (Throwable $exception) {
            return Response::serverError($exception->getMessage());
        }
    }
}
