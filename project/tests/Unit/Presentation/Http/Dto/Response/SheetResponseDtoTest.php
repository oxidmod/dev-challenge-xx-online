<?php
declare(strict_types=1);

use App\Presentation\Http\Dto\Response\SheetResponseDto;

describe('Sheet response DTO test', function () {
    it('serializes sheet with all cells result', function () {
        $data = [
            'cells' => [
                ['id' => 'cell_01', 'value' => '=1+2', 'result' => '3'],
                ['id' => 'cell_02', 'value' => '=1/0', 'result' => null],
            ],
        ];

        $dto = SheetResponseDto::fromSheet(
            $this->createSheetMock($data)
        );

        $this->assertSame(json_encode([
            'cell_01' => ['value' => '=1+2', 'result' => '3'],
            'cell_02' => ['value' => '=1/0', 'result' => 'ERROR'],
        ]), json_encode($dto));
    });
});
