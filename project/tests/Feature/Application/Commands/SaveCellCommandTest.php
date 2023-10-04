<?php
declare(strict_types=1);

use App\Application\Commands\SaveCellCommand;
use App\Domain\NotFoundException;
use App\Domain\Sheet\CalculationException;
use App\Domain\Sheet\Sheet;
use App\Domain\Sheet\SheetsStorageInterface;
use App\Infrastructure\PgSql\ConnectionManager;
use App\Infrastructure\PgSql\PdoConnectionPool;

describe('SaveCellCommand test', function () {
    it('saves cell', function (
        string $value,
        string $expectedResult
    ) {
        $sheetId = 'sheet_01';
        $cellId = 'cell_01';

        /** @var SaveCellCommand $command */
        $command = self::$container->get(SaveCellCommand::class);

        $cell = $command->execute($sheetId, $cellId, $value);

        $this->assertSame($cellId, $cell->getId());
        $this->assertSame($expectedResult, $cell->getResult());

        /** @var Sheet $sheet */
        $sheet = self::$container->get('sheet_storage')->getSheet($sheetId);
        $this->assertEquals($cell, $sheet->getCell($cellId));
    })->with([
        'simple value' => ['42', '42'],
        'formula' => ['=(2 + 3) * 3 / 5', '3']
    ]);

    it('does not save failed values', function () {
        $sheetId = 'sheet_02';
        $cellId = 'cell_02';

        /** @var SaveCellCommand $command */
        $command = self::$container->get(SaveCellCommand::class);

        try {
            $command->execute($sheetId, $cellId, '=1/0');
        } catch (CalculationException $exception) {
            $this->assertFalse($exception->cell->hasResult());
        }

        $this->expectException(NotFoundException::class);

        /** @var Sheet $sheet */
        self::$container->get('sheet_storage')->getSheet($sheetId);
    });
});
