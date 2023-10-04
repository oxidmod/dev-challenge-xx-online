<?php
declare(strict_types=1);

namespace App\Presentation\Http\Middlewares;

use App\Domain\NotFoundException;
use App\Domain\Sheet\CalculationException;
use App\Presentation\Http\Dto\Request\InvalidRequestDtoException;
use App\Presentation\Http\Dto\Response\CellResponseDto;
use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

readonly class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $exception = null;
        try {
            $response = $handler->handle($request);
        } catch (NotFoundException $exception) {
            $response = Response::json(['error' => $exception->getMessage()], 404);
        } catch (InvalidRequestDtoException $exception) {
            $response = Response::json(['error' => $exception], 422);
        } catch (CalculationException $exception) {
            $response = Response::json(CellResponseDto::fromCell($exception->cell), 422);
        } catch (Throwable $exception) {
            $response = Response::serverError($exception->getMessage());
        } finally {
           return $exception ? $response->withHeader('X-Error-Message', $exception->getMessage()) : $response;
        }
    }
}
