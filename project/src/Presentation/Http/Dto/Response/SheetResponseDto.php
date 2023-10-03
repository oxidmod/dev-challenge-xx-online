<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Response;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\Sheet;

class SheetResponseDto extends AbstractJsonResponseDto
{
    private function __construct(array $cells)
    {
        $this->data = $cells;
    }

    public static function fromSheet(Sheet $sheet): self
    {
        return new self(
            array_reduce(
                $sheet->getCells(),
                function (array $result, Cell $cell) {
                    $result[$cell->getId()] = CellResponseDto::fromCell($cell);

                    return $result;
                },
                []
            )
        );
    }
}
