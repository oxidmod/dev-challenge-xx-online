<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

use App\Domain\ValueParser\Formula;
use App\Domain\ValueParser\ValueInterface;
use App\Domain\ValueParser\ValueParser;

class Cell
{
    use IdTrait;

    private bool $containsFormula = false;

    private string $value = '';

    private string $parsedValue = '';

    private array $referencedCellIds = [];

    private array $dependentCellIds = [];

    private ?string $result = null;

    public function __construct(
        private readonly Sheet $sheet,
        private readonly string $id
    ) {
    }

    public function containsFormula(): bool
    {
        return $this->containsFormula;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getParsedValue(): string
    {
        return $this->parsedValue;
    }

    /** @return string[] - List of cell IDs which are used in this cell's formula  */
    public function getReferencedCellIds(): array
    {
        return $this->referencedCellIds;
    }

    public function hasResult(): bool
    {
        return $this->result !== null;
    }

    public function getResult(): string
    {
        return $this->result ?? '';
    }

    public function setNewValue(ValueParser $parser, string $value): void
    {
        $result = $parser->parse($value, $this->sheet);
        if ($this->needsRecalculation($result)) {
            $this->containsFormula = $result instanceof Formula;
            $this->value = $result->getRawValue();
            $this->parsedValue = $result->getParsedValue();
            $this->referencedCellIds = $result->getReferencedCellIds();
            $this->result = null;

            $this->sheet->recalculateCell($this);
        }
    }

    public function setResult(?string $result): void
    {
        $this->result = $result;
    }

    private function needsRecalculation(ValueInterface $value): bool
    {
        $isFormula = $value instanceof Formula;

        return $this->containsFormula !== $isFormula
            || $this->parsedValue !== $value->getParsedValue();
    }
}
