<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

interface ExpressionEvaluatorInterface
{
    /**
     * @param Sheet $sheet
     * @param Cell $updatedCell
     * @param DependencyGraphInterface $dependencyGraph
     *
     * @throws CalculationException
     */
    public function evaluate(Sheet $sheet, Cell $updatedCell, DependencyGraphInterface $dependencyGraph): void;
}
