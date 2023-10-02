<?php
declare(strict_types=1);

namespace App\Presentation\Http;

use OpenSwoole\Core\Psr\Response as BaseResponse;

class Response extends BaseResponse
{
    public static function json($message, int $status = 200): BaseResponse
    {
        $message = !is_string($message) ? json_encode($message) : $message;

        return (new self($message))
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public static function badRequest(string $message): BaseResponse
    {
        return (new self($message))
            ->withStatus(400);
    }

    public static function notFound(string $message = ''): BaseResponse
    {
        return (new self($message))
            ->withStatus(404);
    }

    public static function methodNotAllowed(array $allowedMethods): BaseResponse
    {
        return (new self(''))
            ->withStatus(405)
            ->withHeader('Allow', implode(', ', $allowedMethods));
    }

    public static function serverError(string $message = 'Oops! Something went wrong'): BaseResponse
    {
        return (new self($message))
            ->withStatus(500);
    }
}
