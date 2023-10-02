<?php
declare(strict_types=1);

namespace App\Domain\ValueParser;

interface ValueInterface
{
    public function getParsedValue(): string;

    public function getRawValue(): string;

    public function getReferencedCellIds(): array;
}
