<?php
declare(strict_types=1);

namespace App\Domain;

use RuntimeException;

class NotFoundException extends RuntimeException
{
    public static function sheetNotFound(string $id): self
    {
        return new self(
            sprintf('Sheet "%s" was not found.', $id)
        );
    }

    public static function cellNotFound(string $id): self
    {
        return new self(
            sprintf('Cell "%s" was not found.', $id)
        );
    }
}
