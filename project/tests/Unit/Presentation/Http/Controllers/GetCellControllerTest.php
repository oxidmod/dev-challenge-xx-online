<?php
declare(strict_types=1);

use App\Domain\NotFoundException;
use App\Domain\Sheet\SheetsStorageInterface;
use App\Presentation\Http\Controllers\GetCellController;
use App\Presentation\Http\Dto\Request\InvalidRequestDtoException;
use Psr\Http\Message\ServerRequestInterface;

describe('GET /api/v1/:sheet_id/:cell_id', function () {
    it('returns response', function () {
        $sheetId = 'sheet_01';
        $cellId = 'cell_01';

        $cell = $this->createCellMock(['value' => 'test', 'result' => 'test']);

        $sheet = $this->createSheetMock();
        $sheet->expects($this->once())
            ->method('getCell')
            ->with($cellId)
            ->willReturn($cell);

        $storageMock = $this->createMock(SheetsStorageInterface::class);
        $storageMock->expects($this->once())
            ->method('getSheet')
            ->with($sheetId)
            ->willReturn($sheet);

        $controller = new GetCellController($storageMock);

        $response = $controller($this->createMock(ServerRequestInterface::class), $sheetId, $cellId);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(json_encode(['value' => 'test', 'result' => 'test']), $response->getBody()->getContents());
        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
    });

    it('throws exception with invalid request', function () {
        $controller = new GetCellController($this->createNotCalledMock(SheetsStorageInterface::class));

        $this->expectException(InvalidRequestDtoException::class);

        $controller($this->createMock(ServerRequestInterface::class), '', '');
    });

    it('throws exception when sheet not found', function () {
        $cellId = 'cell_01';
        $sheetId = 'sheet_01';
        $storageMock = $this->createMock(SheetsStorageInterface::class);
        $storageMock->expects($this->once())
            ->method('getSheet')
            ->with($sheetId)
            ->willThrowException(NotFoundException::sheetNotFound($sheetId));

        $controller = new GetCellController($storageMock);

        $this->expectException(NotFoundException::class);

        $controller($this->createMock(ServerRequestInterface::class), $sheetId, $cellId);
    });

    it('throws exception when cell not found', function () {
        $cellId = 'cell_01';
        $sheetId = 'sheet_01';

        $sheet = $this->createSheetMock();
        $sheet->expects($this->once())
            ->method('getCell')
            ->with($cellId)
            ->willThrowException(NotFoundException::cellNotFound($cellId));

        $storageMock = $this->createMock(SheetsStorageInterface::class);
        $storageMock->expects($this->once())
            ->method('getSheet')
            ->with($sheetId)
            ->willReturn($sheet);

        $controller = new GetCellController($storageMock);

        $this->expectException(NotFoundException::class);

        $controller($this->createMock(ServerRequestInterface::class), $sheetId, $cellId);
    });
});