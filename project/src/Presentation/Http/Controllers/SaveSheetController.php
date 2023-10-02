<?php
declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Sheet\SheetsRepositoryInterface;
use App\Domain\ValueParser\ValueParser;
use App\Presentation\Http\Dto\Request\SaveSheetRequestDto;
use App\Presentation\Http\Dto\Response\CellResponseDto;
use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class SaveSheetController
{
    public function __construct(
        private SheetsRepositoryInterface $sheetsRepository,
        private ValueParser $valueParser,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, string $sheetId, string $cellId): ResponseInterface
    {
        $requestDto = new SaveSheetRequestDto($sheetId, $cellId, $request->getParsedBody());

        $sheet = $this->sheetsRepository->getOrCreateSheet($requestDto->sheetId);
        $cell = $sheet->getOrCreateCell($requestDto->cellId);
        $cell->setNewValue($this->valueParser, $requestDto->value);

        return Response::json(CellResponseDto::fromCell($cell), 201);
    }
}
