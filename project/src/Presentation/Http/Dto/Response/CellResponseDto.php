<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Response;

use App\Domain\Sheet\Cell;

class CellResponseDto extends AbstractJsonResponseDto
{
    private function __construct(
        string $value,
        string $result
    ) {
        $this->data = [
            'value' => $value,
            'result' => $result,
        ];
    }

    public static function fromCell(Cell $cell): self
    {
        return new self(
            $cell->getValue(),
            $cell->hasResult() ? $cell->getResult() : 'ERROR',
        );
    }
}
