<?php
declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Application\Commands\SaveCellCommand;
use App\Presentation\Http\Dto\Request\SaveCellRequestDto;
use App\Presentation\Http\Dto\Response\CellResponseDto;
use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class SaveCellController
{
    public function __construct(
        private SaveCellCommand $command
    ) {
    }

    public function __invoke(ServerRequestInterface $request, string $sheetId, string $cellId): ResponseInterface
    {
        $requestDto = new SaveCellRequestDto($sheetId, $cellId, $request->getParsedBody());

        $cell = $this->command->execute($requestDto->sheetId, $requestDto->cellId, $requestDto->value);

        return Response::json(CellResponseDto::fromCell($cell), 201);
    }
}
