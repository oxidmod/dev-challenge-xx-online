<?php
declare(strict_types=1);

namespace App\Application\Commands;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\SheetsStorageInterface;
use App\Domain\ValueParser\ValueParser;

class SaveCellCommand
{
    public function __construct(
        private readonly SheetsStorageInterface $sheetsStorage,
        private readonly ValueParser $valueParser
    ) {
    }

    public function execute(string $sheetId, string $cellId, string $value): Cell
    {
        $sheet = $this->sheetsStorage->getOrCreateSheet($sheetId);
        $cell = $sheet->getOrCreateCell($cellId);

        $cell->setNewValue($this->valueParser, $value);

        $this->sheetsStorage->saveSheet($sheet);

        return $cell;
    }
}
