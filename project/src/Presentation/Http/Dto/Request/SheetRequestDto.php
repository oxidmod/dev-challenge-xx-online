<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Request;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\Sheet;

class SheetRequestDto
{
    use RequestDtoTrait;

    public readonly string $sheetId;
    public readonly string $cellId;

    public function __construct(
        string $sheetId,
        string $cellId,
    ) {
        $this->setSheetId($sheetId);
        $this->setCellId($cellId);

        if ($this->getException()->hasErrors()) {
            throw $this->getException();
        }
    }

    private function setCellId(string $value): void
    {
        if (!Cell::isValidId($value)) {
            $this
                ->getException()
                ->addError(
                    ':cell_id',
                    sprintf('Value must match following pattern: ~[%s]+~', Cell::ALLOWED_ID_SYMBOLS)
                );

            return;
        }

        $this->cellId = strtolower($value);
    }

    private function setSheetId(string $value): void
    {
        if (!Sheet::isValidId($value)) {
            $this
                ->getException()
                ->addError(
                    ':sheet_id',
                    sprintf('Value must match following pattern: ~[%s]+~', Sheet::ALLOWED_ID_SYMBOLS)
                );

            return;
        }

        $this->sheetId = strtolower($value);
    }
}
