<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

trait IdTrait
{
    public const ALLOWED_ID_SYMBOLS = 'A-Za-z0-9_\~\.\-';

    public static function isValidId(string $value): bool
    {
        $regex = sprintf('~[^%s]~', self::ALLOWED_ID_SYMBOLS);

        return !preg_match($regex, $value);
    }
}
