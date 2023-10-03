<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

use App\Domain\NotFoundException;

class Sheet
{
    use IdTrait;

    /** @var Cell[] */
    private array $cells = [];

    public function __construct(
        private readonly string $id,
        private readonly ExpressionEvaluatorInterface $evaluator,
        private readonly int $maxRecursionLevel,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function hasCell(string $id): bool
    {
        return array_key_exists($id, $this->cells);
    }

    public function getCell(string $id): Cell
    {
        if (!$this->hasCell($id)) {
            throw NotFoundException::cellNotFound($id);
        }

        return $this->cells[$id];
    }

    /** @return Cell[] */
    public function getCells(): array
    {
        return array_values($this->cells);
    }

    public function getOrCreateCell(string $id): Cell
    {
        if (!$this->hasCell($id)) {
            $this->cells[$id] = new Cell($this, $id);
        }

        return $this->cells[$id];
    }

    /**
     * Recursively calculates cell value and all dependent cells values
     *
     * @param Cell $cell
     * @return void
     */
    public function recalculateCell(Cell $cell): void
    {
        $this->calculateCell($cell);
        $this->refreshCellsDependencies();
    }

    private function calculateCell(Cell $cell, int $level = 0): void
    {
        if ($level > $this->maxRecursionLevel) {
            throw CalculationException::recursionDepthError($cell);
        }

        $this->resetCellResult($cell);

        $result = $cell->containsFormula() ?
            $this->calculateCellWithFormula($cell) :
            $this->calculateCellWithValue($cell);

        $cell->setResult($result);

        foreach ($cell->getDependentCellIds() as $cellId) {
            $this->calculateCell($this->getCell($cellId), $level + 1);
        }
    }

    private function calculateCellWithFormula(Cell $cell): string
    {
        return $this->evaluator->evaluate($this, $cell);
    }

    private function calculateCellWithValue(Cell $cell): string
    {
        # There is no calculations, just a simple value
        return $cell->getParsedValue();
    }

    private function refreshCellsDependencies(): void
    {
        $dependencies = [];
        foreach ($this->cells as $cell) {
            foreach ($cell->getReferencedCellIds() as $refId) {
                $dependencies[$refId][$cell->getId()] = 1;
            }
        }

        foreach ($dependencies as $cellId => $dependentCellIds) {
            $this->getCell($cellId)->setDependentCellIds(array_keys($dependentCellIds));
        }
    }

    /**
     * Reset cell result and all dependent cells to prevent miscalculation
     *
     * @param Cell $cell
     * @return void
     */
    private function resetCellResult(Cell $cell): void
    {
        $cell->setResult(null);
        foreach ($cell->getDependentCellIds() as $cellId) {
            $this->resetCellResult($this->getCell($cellId));
        }
    }
}
