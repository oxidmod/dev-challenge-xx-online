<?php
declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Sheet\SheetsStorageInterface;
use App\Presentation\Http\Dto\Request\CellRequestDto;
use App\Presentation\Http\Dto\Response\CellResponseDto;
use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class GetCellController
{
    public function __construct(
        private SheetsStorageInterface $sheetsStorage,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, string $sheetId, string $cellId): ResponseInterface
    {
        $requestDto = new CellRequestDto($sheetId, $cellId);

        $sheet = $this->sheetsStorage->getSheet($requestDto->sheetId);
        $cell = $sheet->getCell($cellId);

        return Response::json(CellResponseDto::fromCell($cell));
    }
}
