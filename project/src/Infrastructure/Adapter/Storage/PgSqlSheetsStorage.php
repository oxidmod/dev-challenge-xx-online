<?php
declare(strict_types=1);

namespace App\Infrastructure\Adapter\Storage;

use App\Domain\NotFoundException;
use App\Domain\Sheet\Cell;
use App\Domain\Sheet\DependencyGraphFactoryInterface;
use App\Domain\Sheet\ExpressionEvaluatorInterface;
use App\Domain\Sheet\Sheet;
use App\Domain\Sheet\SheetsStorageInterface;
use App\Infrastructure\PgSql\ConnectionAwareInterface;
use App\Infrastructure\PgSql\ConnectionAwareTrait;
use PDO;

class PgSqlSheetsStorage implements ConnectionAwareInterface, SheetsStorageInterface
{
    use ConnectionAwareTrait;

    public function __construct(
        private readonly DependencyGraphFactoryInterface $dependencyGraphFactory,
        private readonly ExpressionEvaluatorInterface $expressionEvaluator,
    ) {
    }

    public function getOrCreateSheet(string $sheetId): Sheet
    {
        $rows = $this->loadCells($sheetId, true);

        return $this->createSheet($sheetId, $rows);
    }

    public function getSheet(string $sheetId): Sheet
    {
        $rows = $this->loadCells($sheetId);

        if (empty($rows)) {
            throw NotFoundException::sheetNotFound($sheetId);
        }

        return $this->createSheet($sheetId, $rows);
    }

    public function saveSheet(Sheet $sheet): void
    {
        $rows = array_map(function (Cell $cell) use ($sheet) {
            return [
                'sheet_id' => $sheet->getId(),
                'cell_id' => $cell->getId(),
                'props' => json_encode(array_filter([
                    'contains_formula' => $cell->containsFormula(),
                    'value' => $cell->getValue(),
                    'parsed_value' => $cell->getParsedValue(),
                    'referenced_cell_ids' => $cell->getReferencedCellIds(),
                    'result' => $cell->hasResult() ? $cell->getResult() : null,
                ])),
            ];
        }, $sheet->getCells());

        if (!empty($rows)) {
            $this->upsertCells($rows);
        }
    }

    private function createSheet(string $id, array $rows): Sheet
    {
        $sheet = new Sheet(
            $id,
            $this->dependencyGraphFactory,
            $this->expressionEvaluator,
        );

        $constructor = (function (array $cells): Sheet {
            $this->cells = $cells;
            return $this;
        })(...);

        $cells = array_reduce(
            $rows,
            function (array $result, array $row) use ($sheet) {
                $result[$row['cell_id']] = $this->createCell($sheet, $row);

                return $result;
            },
            []
        );

        return $constructor->call($sheet, $cells);
    }

    private function createCell(Sheet $sheet, array $row): Cell
    {
        $cell = new Cell($sheet, $row['cell_id']);

        $constructor = (function (array $props): Cell {
            $this->containsFormula = $props['contains_formula'] ?? false;
            $this->value = $props['value'] ?? '';
            $this->parsedValue = $props['parsed_value'] ?? '';
            $this->referencedCellIds = $props['referenced_cell_ids'] ?? [];
            $this->result = $props['result'] ?? null;

            return $this;
        })(...);

        return $constructor->call($cell, json_decode($row['props'], true));
    }

    private function loadCells(string $sheetId, bool $forUpdate = false): array
    {
        $query = 'SELECT * FROM sheets WHERE sheet_id = :sheet_id ORDER BY sheet_id, cell_id';
        if ($forUpdate) {
            $query .= ' FOR UPDATE';
        }

        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':sheet_id', $sheetId);
        $stmt->execute([':sheet_id' => $sheetId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function upsertCells(array $rows): void
    {
        $query = <<<UPSERT
INSERT INTO sheets (
    sheet_id, 
    cell_id, 
    props
) VALUES (
    :sheet_id,
    :cell_id, 
    :props
) ON CONFLICT(sheet_id, cell_id)  DO UPDATE 
SET props = EXCLUDED.props
UPSERT;

        $stmt = $this->connection->prepare($query);
        foreach ($rows as $row) {
            $stmt->bindParam(':sheet_id', $row['sheet_id']);
            $stmt->bindParam(':cell_id', $row['cell_id']);
            $stmt->bindParam(':props', $row['props']);
            $stmt->execute($row);
        }
    }
}
