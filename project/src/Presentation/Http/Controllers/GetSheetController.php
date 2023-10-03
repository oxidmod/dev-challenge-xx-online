<?php
declare(strict_types=1);

namespace App\Presentation\Http\Controllers;

use App\Domain\Sheet\SheetsStorageInterface;
use App\Presentation\Http\Dto\Request\SheetRequestDto;
use App\Presentation\Http\Dto\Response\SheetResponseDto;
use App\Presentation\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class GetSheetController
{
    public function __construct(
        private SheetsStorageInterface $sheetsRepository,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, string $sheetId): ResponseInterface
    {
        $requestDto = new SheetRequestDto($sheetId);

        $sheet = $this->sheetsRepository->getSheet($requestDto->sheetId);

        return Response::json(SheetResponseDto::fromSheet($sheet));
    }
}
