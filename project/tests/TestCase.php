<?php
declare(strict_types=1);

namespace App\Tests;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\Sheet;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function createCellMock(array $data = []): Cell
    {
        $result = array_key_exists('result', $data) ? $data['result'] : ($data['value'] ?? null);

        $mock = $this->createMock(Cell::class);
        $mock->expects($this->any())->method('getId')->willReturn($data['id'] ?? uniqid('cell_'));
        $mock->expects($this->any())->method('getValue')->willReturn($data['value'] ?? '');
        $mock->expects($this->any())->method('getParsedValue')->willReturn($data['parsed_value'] ?? $data['value'] ?? '');
        $mock->expects($this->any())->method('getResult')->willReturn($result ?? '');
        $mock->expects($this->any())->method('hasResult')->willReturn($result !== null);

        return $mock;
    }

    protected function createSheetMock(array $data = []): Sheet
    {
        $mock = $this->createMock(Sheet::class);
        $mock->expects($this->any())->method('getId')->willReturn($data['id'] ?? uniqid('sheet_'));

        $cells = array_map(fn (array $cellData) => $this->createCellMock($cellData), $data['cells'] ?? []);
        $mock->expects($this->any())->method('getCells')->willReturn($cells);

        return $mock;
    }
}
