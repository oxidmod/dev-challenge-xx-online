<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

interface DependencyGraphInterface
{
    /**
     * Returns TRUE if $cell depends on $fromCell
     * @param Cell $cell
     * @param Cell $fromCell
     * @return bool
     */
    public function hasDependency(Cell $cell, Cell $fromCell): bool;

    /**
     * Returns array of cell IDs in correct order for calculation
     * @return array
     */
    public function getCalculationOrder(): array;
}
