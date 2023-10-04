<?php
declare(strict_types=1);

use App\Presentation\Http\Dto\Request\CellRequestDto;
use App\Presentation\Http\Dto\Request\InvalidRequestDtoException;

describe('Cell request DTO test', function () {
    it('accepts valid data', function () {
        $sheetId = '0-9aZ~._.~';
        $cellId = '~._.~zA0-9';

        $dto = new CellRequestDto($sheetId, $cellId);

        $this->assertSame('0-9az~._.~', $dto->sheetId);
        $this->assertSame('~._.~za0-9', $dto->cellId);
    });

    it('throws exception with invalid data', function (string $sheetId, string $cellId) {
        $this->expectException(InvalidRequestDtoException::class);
        $this->expectExceptionMessage('Invalid data was given.');

        new CellRequestDto($sheetId, $cellId);
    })->with([
        'invalid sheet id' => ['not allowed+id/', '0-9aZ~._.~'],
        'invalid cell id' => ['0-9aZ~._.~', 'not allowed+id/'],
        'invalid both ids' => ['not allowed+id/', 'with*math=ops'],
    ]);
});
