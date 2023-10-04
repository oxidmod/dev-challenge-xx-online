<?php
declare(strict_types=1);

use App\Application\Commands\SaveCellCommand;
use App\Domain\Sheet\CalculationException;
use App\Presentation\Http\Controllers\SaveCellController;
use App\Presentation\Http\Dto\Request\InvalidRequestDtoException;
use Psr\Http\Message\ServerRequestInterface;

describe('POST /api/v1/:sheet_id/:cell_id', function () {
    it('saves cell and return response', function () {
        $sheetId = 'sheet_01';
        $cellId = 'cell_01';
        $value = 'test';

        $cell = $this->createCellMock(['value' => $value, 'result' => $value]);

        $commandMock = $this->createMock(SaveCellCommand::class);
        $commandMock->expects($this->once())
            ->method('execute')
            ->with($sheetId, $cellId, $value)
            ->willReturn($cell);

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['value' => $value]);

        $controller = new SaveCellController($commandMock);

        $response = $controller($request, $sheetId, $cellId,);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(json_encode(['value' => 'test', 'result' => 'test']), $response->getBody()->getContents());
        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
    });

    it('throws exception with invalid request', function () {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([]);

        $controller = new SaveCellController(
            $this->createNotCalledMock(SaveCellCommand::class)
        );

        $this->expectException(InvalidRequestDtoException::class);

        $controller($request, 'sheet_id', 'cell_id');
    });

    it('throws exception when cell can\'t be saved', function () {
        $sheetId = 'sheet_01';
        $cellId = 'cell_01';
        $value = '=1/0';

        $cell = $this->createCellMock();

        $commandMock = $this->createMock(SaveCellCommand::class);
        $commandMock->expects($this->once())
            ->method('execute')
            ->with($sheetId, $cellId, $value)
            ->willThrowException(CalculationException::calculationError($cell));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->expects($this->once())
            ->method('getParsedBody')
            ->willReturn(['value' => $value]);

        $controller = new SaveCellController($commandMock);

        $this->expectException(CalculationException::class);

        $controller($request, $sheetId, $cellId);
    });
});
