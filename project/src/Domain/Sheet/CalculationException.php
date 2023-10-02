<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

use DomainException;
use Throwable;

class CalculationException extends DomainException
{
    private function __construct(
        public readonly Cell $cell,
        string $message,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, previous: $previous);
    }

    public static function calculationError(Cell $cell, ?Throwable $previous = null): self
    {
        return new self(
            cell: $cell,
            message: 'Error occurred during calculation.',
            previous: $previous,
        );
    }

    public static function circularReference(Cell $cell): self
    {
        return new self(
            cell: $cell,
            message: 'Circular reference was found during sheet calculation.',
        );
    }

    public static function recursionDepthError(Cell $cell): self
    {
        return new self(
            cell: $cell,
            message: 'Max recursion level was reached.',
        );
    }
}
