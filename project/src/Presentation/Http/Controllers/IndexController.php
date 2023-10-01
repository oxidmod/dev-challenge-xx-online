<?php
declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return Response::json([
            'app' => 'Backend | DEV Challenge XX | Online round',
            'api_url_prefix' => '/api/v1',
        ]);
    }
}
