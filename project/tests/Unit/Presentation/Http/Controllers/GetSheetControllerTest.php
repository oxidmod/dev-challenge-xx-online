<?php
declare(strict_types=1);

use App\Domain\NotFoundException;
use App\Domain\Sheet\SheetsStorageInterface;
use App\Presentation\Http\Controllers\GetSheetController;
use App\Presentation\Http\Dto\Request\InvalidRequestDtoException;
use Psr\Http\Message\ServerRequestInterface;

describe('GET /api/v1/:sheet_id', function () {
    it('returns response', function () {
        $sheetId = 'sheet_01';

        $sheet = $this->createSheetMock([
            'cells' => [
                ['id' => 'cell_01', 'value' => '=1+2', 'result' => '3'],
            ],
        ]);

        $storageMock = $this->createMock(SheetsStorageInterface::class);
        $storageMock->expects($this->once())
            ->method('getSheet')
            ->with($sheetId)
            ->willReturn($sheet);

        $controller = new GetSheetController($storageMock);

        $response = $controller($this->createMock(ServerRequestInterface::class), $sheetId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode(['cell_01' => ['value' => '=1+2', 'result' => '3']]), $response->getBody()->getContents());
        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
    });

    it('throws exception with invalid request', function () {
        $controller = new GetSheetController($this->createNotCalledMock(SheetsStorageInterface::class));

        $this->expectException(InvalidRequestDtoException::class);

        $controller($this->createMock(ServerRequestInterface::class), '');
    });

    it('throws exception when sheet not found', function () {
        $sheetId = 'sheet_01';
        $storageMock = $this->createMock(SheetsStorageInterface::class);
        $storageMock->expects($this->once())
            ->method('getSheet')
            ->with($sheetId)
            ->willThrowException(NotFoundException::sheetNotFound($sheetId));

        $controller = new GetSheetController($storageMock);

        $this->expectException(NotFoundException::class);

        $controller($this->createMock(ServerRequestInterface::class), $sheetId);
    });
});