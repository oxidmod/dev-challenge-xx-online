<?php
declare(strict_types=1);

namespace App\Domain\ValueParser;

readonly class Formula implements ValueInterface
{
    public function __construct(
        private string $rawValue,
        private string $parsedValue,
        private array $referencedCellIds,
    ) {
    }

    public function getParsedValue(): string
    {
        return $this->parsedValue;
    }

    public function getRawValue(): string
    {
        return $this->rawValue;
    }

    public function getReferencedCellIds(): array
    {
        return $this->referencedCellIds;
    }
}
