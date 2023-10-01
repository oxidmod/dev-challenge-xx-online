<?php
declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Presentation\Http\Dto\InvalidDtoException;
use App\Presentation\Http\Dto\SaveSheetRequestDto;
use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SaveSheetController
{
    public function __invoke(ServerRequestInterface $request, string $sheetId, string $cellId): ResponseInterface
    {
        try {
            $requestDto = new SaveSheetRequestDto($sheetId, $cellId, $request->getParsedBody());

            return Response::json([]);
        } catch (InvalidDtoException $exception) {
            return Response::json($exception, 422);
        }
    }
}
