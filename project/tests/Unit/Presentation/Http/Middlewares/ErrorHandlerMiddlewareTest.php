<?php
declare(strict_types=1);

use App\Domain\NotFoundException;
use App\Domain\Sheet\CalculationException;
use App\Presentation\Http\Dto\Request\InvalidRequestDtoException;
use App\Presentation\Http\Middlewares\ErrorHandlerMiddleware;
use App\Presentation\Http\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

describe('Error handler middleware', function () {
    it('returns the same response', function () {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(Response::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $middleware = new ErrorHandlerMiddleware();

        $this->assertSame($response, $middleware->process($request, $handler));
    });

    it('returns 404 on NotFound exception', function () {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willThrowException(NotFoundException::cellNotFound('cell_01'));

        $middleware = new ErrorHandlerMiddleware();

        $response = $middleware->process($request, $handler);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
        $this->assertSame(['Cell "cell_01" was not found.'], $response->getHeader('X-Error-Message'));
        $this->assertSame(json_encode(['error' => 'Cell "cell_01" was not found.']), $response->getBody()->getContents());
    });

    it('returns 422 on InvalidRequestDtoException exception', function () {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willThrowException((new InvalidRequestDtoException)
                ->addError('field1', 'error1')
                ->addError('field1', 'error2')
                ->addError('field2', 'error3')
            );

        $middleware = new ErrorHandlerMiddleware();

        $response = $middleware->process($request, $handler);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
        $this->assertSame(['Invalid data was given.'], $response->getHeader('X-Error-Message'));
        $this->assertSame(json_encode([
            'error' => [
                'field1' => ['error1', 'error2'],
                'field2' => ['error3'],
            ],
        ]), $response->getBody()->getContents());
    });

    it('returns 422 on CalculationException', function () {
        $request = $this->createMock(ServerRequestInterface::class);

        $cell = $this->createCellMock(['value' => '=2/0', 'result' => null]);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willThrowException(CalculationException::calculationError($cell));

        $middleware = new ErrorHandlerMiddleware();

        $response = $middleware->process($request, $handler);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
        $this->assertSame(['Error occurred during calculation.'], $response->getHeader('X-Error-Message'));
        $this->assertSame(json_encode(['value' => '=2/0', 'result' => 'ERROR']), $response->getBody()->getContents());
    });

    it('returns 500 on unexpected exception', function () {
        $request = $this->createMock(ServerRequestInterface::class);

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willThrowException(new Exception('test'));

        $middleware = new ErrorHandlerMiddleware();

        $response = $middleware->process($request, $handler);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
        $this->assertSame(['test'], $response->getHeader('X-Error-Message'));
        $this->assertSame(json_encode(['error' => 'test']), $response->getBody()->getContents());
    });
});