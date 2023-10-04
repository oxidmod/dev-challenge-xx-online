<?php
declare(strict_types=1);

use App\Presentation\Http\Dto\Response\CellResponseDto;

describe('Cell response DTO test', function () {
    it('serializes cell with result', function () {
        $data = ['value' => '=1+2', 'result' => '3'];
        $dto = CellResponseDto::fromCell(
            $this->createCellMock($data)
        );

        $this->assertSame(json_encode($data), json_encode($dto));
    });

    it('serializes cell without result', function () {
        $data =  ['value' => '=1/0', 'result' => null];
        $dto = CellResponseDto::fromCell(
            $this->createCellMock($data)
        );

        $this->assertSame(json_encode(['value' => '=1/0', 'result' => 'ERROR']), json_encode($dto));
    });
});