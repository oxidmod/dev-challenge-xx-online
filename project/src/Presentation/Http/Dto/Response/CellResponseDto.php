<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Response;

use App\Domain\Sheet\Cell;
use JsonSerializable;

readonly class CellResponseDto implements JsonSerializable
{
    private array $data;

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

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}
