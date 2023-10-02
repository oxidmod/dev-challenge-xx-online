<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

interface ExpressionEvaluatorInterface
{
    /**
     * @param Sheet $sheet
     * @param Cell $cell
     * @return string
     *
     * @throws CalculationException
     */
    public function evaluate(Sheet $sheet, Cell $cell): string;
}
