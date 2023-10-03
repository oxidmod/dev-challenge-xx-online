<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Request;

use App\Domain\Sheet\Sheet;

class SheetRequestDto
{
    use RequestDtoTrait;

    public readonly string $sheetId;

    public function __construct(
        string $sheetId,
    ) {
        $this->setSheetId($sheetId);

        if ($this->getException()->hasErrors()) {
            throw $this->getException();
        }
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
