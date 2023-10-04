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
        private readonly DependencyGraphFactoryInterface $dependencyGraphFactory,
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
     * @param Cell $cell
     * @return void
     */
    public function recalculateCell(Cell $cell): void
    {
        $dependencyGraph = $this->dependencyGraphFactory->create($this, $cell);

        $this->resetDependenciesResult($dependencyGraph, $cell);

        if (!$cell->containsFormula()) {
            $cell->setResult($cell->getParsedValue());
        }

        $this->evaluator->evaluate($this, $cell, $dependencyGraph);
    }

    private function resetDependenciesResult(DependencyGraphInterface $dependencyGraph, Cell $updatedCell): void
    {
        foreach ($this->cells as $cell) {
            if ($cell === $updatedCell) {
                continue;
            }

            if ($dependencyGraph->hasDependency($cell, $updatedCell)) {
                $cell->setResult(null);
            }
        }
    }
}
