<?php
declare(strict_types=1);

use App\Presentation\Http\Dto\Request\InvalidRequestDtoException;
use App\Presentation\Http\Dto\Request\SaveCellRequestDto;

describe('Save cell request DTO test', function () {
    it('accepts valid data', function () {
        $sheetId = '0-9aZ~._.~';
        $cellId = '~._.~zA0-9';
        $data = ['value' => 'aBc'];

        $dto = new SaveCellRequestDto($sheetId, $cellId, $data);

        $this->assertSame('0-9az~._.~', $dto->sheetId);
        $this->assertSame('~._.~za0-9', $dto->cellId);
        $this->assertSame('abc', $dto->value);
    });

    it('throws exception with invalid data', function (string $sheetId, string $cellId, array $data) {
        $this->expectException(InvalidRequestDtoException::class);
        $this->expectExceptionMessage('Invalid data was given.');

        new SaveCellRequestDto($sheetId, $cellId, $data);
    })->with([
        'invalid sheet id' => ['not allowed+id/', '0-9aZ~._.~', ['value' => '=1+2']],
        'invalid cell id' => ['0-9aZ~._.~', 'not allowed+id/', ['value' => '1+2']],
        'invalid data (empty data)' => ['not allowed+id/', '0-9aZ~._.~', ['value1' => '1']],
        'invalid data (empty formula)' => ['not allowed+id/', '0-9aZ~._.~', ['value' => '=']],
        'invalid both ids and data' => ['not allowed+id/', 'not allowed+id/', ['value' => '=']],
    ]);
});