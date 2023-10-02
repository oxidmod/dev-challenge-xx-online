<?php
declare(strict_types=1);

namespace App\Domain\ValueParser;

readonly class Value implements ValueInterface
{
    public function __construct(
        private string $value
    ) {
    }

    public function getParsedValue(): string
    {
        return $this->value;
    }

    public function getRawValue(): string
    {
        return $this->value;
    }

    public function getReferencedCellIds(): array
    {
        return [];
    }
}
