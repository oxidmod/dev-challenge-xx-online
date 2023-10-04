<?php
declare(strict_types=1);

namespace App\Infrastructure\Adapter\Expression;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\DependencyGraphInterface;
use App\Domain\Sheet\ExpressionEvaluatorInterface;
use App\Domain\Sheet\CalculationException;
use App\Domain\Sheet\Sheet;
use App\Domain\ValueParser\ValueParser;
use ChrisKonnertz\StringCalc\StringCalc;
use Throwable;

class Evaluator implements ExpressionEvaluatorInterface
{
    public function __construct(
        private readonly StringCalc $calculator
    ) {
    }

    public function evaluate(Sheet $sheet, Cell $updatedCell, DependencyGraphInterface $dependencyGraph): void
    {
        $orderedCellIds = $dependencyGraph->getCalculationOrder();

        foreach ($orderedCellIds as $cellId) {
            try {
                $cell = $sheet->getCell($cellId);

                if ($cell->hasResult()) continue;

                $cell->setResult(
                    $this->doEvaluateCell($sheet, $cell)
                );
            } catch (Throwable $exception) {
                # As value valid value in the updated cell could cause errors in dependent cells we do not want to save it
                $updatedCell->setResult(null);

                throw CalculationException::calculationError($updatedCell, $exception);
            }
        }
    }

    private function doEvaluateCell(Sheet $sheet, Cell $cell): string
    {
        $referencedCellValues = array_reduce(
            $cell->getReferencedCellIds(),
            function (array $result, string $refId) use ($sheet, $cell) {
                $ref = $sheet->getCell($refId);
                $result[ValueParser::formatCellIdPlaceholder($refId)] = $ref->getResult();

                return $result;
            },
            []
        );

        $expression = $cell->getParsedValue();
        if (!empty($referencedCellValues)) {
            $expression = str_replace(
                array_keys($referencedCellValues),
                array_values($referencedCellValues),
                $expression
            );
        }

        return (string)$this->calculator->calculate($expression);
    }
}
