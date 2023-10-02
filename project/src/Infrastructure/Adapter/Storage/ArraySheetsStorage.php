<?php
declare(strict_types=1);

namespace App\Infrastructure\Adapter\Storage;

use App\Domain\NotFoundException;
use App\Domain\Sheet\ExpressionEvaluatorInterface;
use App\Domain\Sheet\Sheet;
use App\Domain\Sheet\SheetsRepositoryInterface;

class ArraySheetsStorage implements SheetsRepositoryInterface
{
    public function __construct(
        private readonly ExpressionEvaluatorInterface $expressionEvaluator,
        private readonly int $allowedRecursionLevel,
        private array $sheets = []
    ) {
    }

    public function getSheet(string $sheetId): Sheet
    {
        return $this->sheets[$sheetId] ?? throw NotFoundException::sheetNotFound($sheetId);
    }

    public function getOrCreateSheet(string $sheetId): Sheet
    {
        if (!array_key_exists($sheetId, $this->sheets)) {
            $this->sheets[$sheetId] = new Sheet($sheetId, $this->expressionEvaluator, $this->allowedRecursionLevel);
        }

        return $this->sheets[$sheetId];
    }

    public function putSheet(Sheet $sheet): void
    {
        $this->sheets[$sheet->getId()] = $sheet;
    }
}
