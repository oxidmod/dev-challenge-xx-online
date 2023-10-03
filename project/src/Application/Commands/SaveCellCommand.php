<?php
declare(strict_types=1);

namespace App\Application\Commands;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\SheetsStorageInterface;
use App\Domain\ValueParser\ValueParser;

readonly class SaveCellCommand
{
    public function __construct(
        private SheetsStorageInterface $sheetsStorage,
        private ValueParser $valueParser
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
