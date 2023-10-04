<?php
declare(strict_types=1);

namespace App\Domain\ValueParser;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\Sheet;

class ValueParser
{
    public static function formatCellIdPlaceholder(string $cellId): string
    {
        return sprintf('##val(%s)##', $cellId);
    }

    public function parse(string $value, Sheet $sheet): ValueInterface
    {
        if ($this->isFormula($value)) {
            [$parsedValue, $referencedCellIds] = $this->parseFormula($value, $sheet);

            return new Formula(
                $value,
                $parsedValue,
                $referencedCellIds,
            );
        }

        return new Value($value);
    }

    private function isFormula(string $value): bool
    {
        # starts with = and does not contain any other = symbols
        return str_starts_with($value, '=') && strpos($value, '=', 1) === false;
    }

    private function parseFormula(string $value, Sheet $sheet): array
    {
        $referencedIds = [];
        $parsedValue = preg_replace_callback(
            '~(['. Cell::ALLOWED_ID_SYMBOLS .']+)~',
            function (array $matches) use ($sheet, &$referencedIds) {
                $value = $matches[0];

                # put a placeholder if sheet contains cell
                if ($sheet->hasCell($value)) {
                    return $this->cellIdValue($value, $referencedIds);
                }

                # Sign "-" could be a part of cellId or just an operator
                if (str_contains($value, '-')) {
                    return $this->parseCellIdWithMinusSign($value, $sheet, $referencedIds);
                }

                # No cell IDs, no "-" signs... Let's return as raw value and see what would happen
                return $value;
            },
            $value,
        );

        return [substr($parsedValue, 1), array_keys($referencedIds)];
    }

    private function parseCellIdWithMinusSign(string $value, Sheet $sheet, array &$referencedIds): string
    {
        $len = strlen($value);
        if ($len === 0) {
            return '';
        }

        # Looking for cell ID from string beginning
        for ($i = 0; $i < $len; $i++) {
            $cellId = substr($value, 0, $i + 1);

            if (!$sheet->hasCell($cellId)) {
                continue;
            }

            # Replace cell ID and parse the rest
            return sprintf(
                '%s%s',
                $this->cellIdValue($cellId, $referencedIds),
                $this->parseCellIdWithMinusSign(substr($value, $i + 1), $sheet, $referencedIds)
            );
        }

        if ($len > 1) {
            return sprintf(
                '%s%s',
                substr($value, 0, 1),
                $this->parseCellIdWithMinusSign(substr($value, 1), $sheet, $referencedIds)
            );
        }

        return $value;
    }

    private function cellIdValue(string $cellId, array &$referencedCellIds): string
    {
        $referencedCellIds[$cellId] = 1;

        return self::formatCellIdPlaceholder($cellId);
    }
}
