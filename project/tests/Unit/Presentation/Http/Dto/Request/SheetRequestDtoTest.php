<?php
declare(strict_types=1);

use App\Presentation\Http\Dto\Request\InvalidRequestDtoException;
use App\Presentation\Http\Dto\Request\SheetRequestDto;

describe('Sheet request DTO test', function () {
    it('accepts valid data', function () {
        $sheetId = '0-9aZ~._.~';

        $dto = new SheetRequestDto($sheetId);

        $this->assertSame('0-9az~._.~', $dto->sheetId);
    });

    it('throws exception with invalid id', function () {
        $sheetId = 'not allowed+id/';

        $this->expectException(InvalidRequestDtoException::class);
        $this->expectExceptionMessage('Invalid data was given.');

        new SheetRequestDto($sheetId);
    });

    it('throws exception with empty id', function () {
        $sheetId = '';

        $this->expectException(InvalidRequestDtoException::class);
        $this->expectExceptionMessage('Invalid data was given.');

        new SheetRequestDto($sheetId);
    });
});