<?php
declare(strict_types=1);

namespace App\Presentation\Http\Middlewares;

use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class ParseJsonMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isJsonContentType($request)) {
            $data = json_decode($request->getBody()->getContents(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return Response::badRequest(json_last_error_msg());
            }

            $request = $request->withParsedBody($data);
        }

        return $handler->handle($request);
    }

    private function isJsonContentType(ServerRequestInterface $request): bool
    {
        foreach ((array)$request->getHeader('Content-type') as $contentType) {
            if (preg_match('~application\/json~', $contentType)) {
                return true;
            }
        }

        return false;
    }
}
