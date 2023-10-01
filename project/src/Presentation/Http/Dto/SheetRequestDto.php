<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto;

class SheetRequestDto
{
    use RequestDtoTrait;

    private const ALLOWED_ID_SYMBOLS = 'A-Za-z0-9_\~\.\-';

    public function __construct(
        public readonly string $sheetId,
        public readonly string $cellId,
    ) {
        $this->validateId(':sheet_id', $this->sheetId);
        $this->validateId(':cell_id', $this->cellId);

        if ($this->getException()->hasErrors()) {
            throw $this->getException();
        }
    }

    private function validateId(string $field, string $value): void
    {
        $regex = sprintf('~[^%s]~', self::ALLOWED_ID_SYMBOLS);
        if (preg_match($regex, $value)) {
            $this->getException()->addError($field, sprintf('Value must match following pattern: ~[%s]+~', self::ALLOWED_ID_SYMBOLS));
        }
    }
}
