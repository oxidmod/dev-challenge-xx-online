<?php
declare(strict_types=1);

namespace App\Presentation\Http;

use OpenSwoole\Core\Psr\Response as BaseResponse;

class Response extends BaseResponse
{
    public static function json($data, int $status = 200): BaseResponse
    {
        $data = !is_string($data) ? json_encode($data) : $data;

        return (new self($data))
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public static function badRequest(string $reason): BaseResponse
    {
        return (new self($reason))
            ->withStatus(400);
    }

    public static function notFound(): BaseResponse
    {
        return (new self(''))
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
