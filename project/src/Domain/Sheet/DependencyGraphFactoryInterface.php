<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

interface DependencyGraphFactoryInterface
{
    /**
     * Creates dependency graph for sheet
     * Throws CalculationException in case if $updatedCell leads to circular references
     *
     * @param Sheet $sheet
     * @param Cell $updatedCell
     * @return DependencyGraphInterface
     *
     * @throws CalculationException
     */
    public function create(Sheet $sheet, Cell $updatedCell): DependencyGraphInterface;
}
