<?php
declare(strict_types=1);

use App\Domain\NotFoundException;
use App\Domain\Sheet\SheetsStorageInterface;
use App\Domain\ValueParser\ValueParser;
use App\Infrastructure\Adapter\Storage\PgSqlSheetsStorage;

describe('PgSqlSheetsStorage adapter test', function () {
    it('it creates new sheet', function () {
        /** @var SheetsStorageInterface $storage */
        $storage = self::getContainer()->get('sheet_storage');

        $sheetId = uniqid('sheet_');

        $sheet = $storage->getOrCreateSheet($sheetId);

        $this->assertSame($sheetId, $sheet->getId());
        $this->assertEmpty($sheet->getCells());

        $this->expectException(NotFoundException::class);
        $storage->getSheet($sheetId);
    });

    it('saves sheet', function () {
        /** @var SheetsStorageInterface $storage */
        $storage = self::getContainer()->get('sheet_storage');
        $this->assertInstanceOf(PgSqlSheetsStorage::class, $storage);

        $sheetId = uniqid('sheet_');
        $sheet = $storage->getOrCreateSheet($sheetId);

        $valueParser = new ValueParser();

        $cellId1 = uniqid('cell_');
        $cell1 = $sheet->getOrCreateCell($cellId1);
        $this->assertSame($cellId1, $cell1->getId());
        $cell1->setNewValue($valueParser, '=7+3');

        $cellId2 = uniqid('cell_');
        $cell2 = $sheet->getOrCreateCell($cellId2);
        $this->assertSame($cellId2, $cell2->getId());
        $cell2->setNewValue($valueParser, '10');

        $cellId3 = uniqid('cell_');
        $cell3 = $sheet->getOrCreateCell($cellId3);
        $this->assertSame($cellId3, $cell3->getId());
        $cell3->setNewValue($valueParser, "=$cellId1 + $cellId2");

        $storage->saveSheet($sheet);

        $sheetFromDb = $storage->getSheet($sheetId);
        $this->assertSame($sheetId, $sheetFromDb->getId());
        $this->assertCount(3, $sheetFromDb->getCells());
        $this->assertSame('10', $sheetFromDb->getCell($cellId1)->getResult());
        $this->assertSame('10', $sheetFromDb->getCell($cellId2)->getResult());
        $this->assertSame('20', $sheetFromDb->getCell($cellId3)->getResult());
    });
});