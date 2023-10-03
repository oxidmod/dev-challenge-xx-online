<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Request;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\Sheet;

class CellRequestDto extends SheetRequestDto
{
    public readonly string $cellId;

    public function __construct(
        string $sheetId,
        string $cellId,
    ) {
        $this->setCellId($cellId);

        parent::__construct($sheetId);
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
}
