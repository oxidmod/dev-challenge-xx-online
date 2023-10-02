<?php
declare(strict_types=1);

namespace App\Infrastructure\Adapter\Expression;

use App\Domain\Sheet\Cell;
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

    public function evaluate(Sheet $sheet, Cell $cell): string
    {
        $referencedCellValues = array_reduce(
            $cell->getReferencedCellIds(),
            function (array $result, string $refId) use ($sheet, $cell) {
                $ref = $sheet->getCell($refId);
                if (!$ref->hasResult()) {
                    throw CalculationException::circularReference($ref);
                }

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
                $cell->getParsedValue()
            );
        }

        try {
            return (string)$this->calculator->calculate(substr($expression, 1));
        } catch (Throwable $exception) {
            throw CalculationException::calculationError($cell, $exception);
        }
    }
}
