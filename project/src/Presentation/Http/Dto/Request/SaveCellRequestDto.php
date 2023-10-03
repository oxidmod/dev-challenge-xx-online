<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Request;

class SaveCellRequestDto extends CellRequestDto
{
    public readonly string $value;

    public function __construct(
        string $sheetId,
        string $cellId,
        array $data
    ) {
        $this->setValueFromData($data);

        parent::__construct($sheetId, $cellId);
    }

    private function setValueFromData(array $data): void
    {
        if (!array_key_exists('value', $data)) {
            $this->getException()->addError(':value', 'Field is required');
            return;
        }

        $this->value = strtolower((string)$data['value']);

        if ($this->value === '=') {
            $this->getException()->addError(':value', 'Expression is required');
        }
    }
}
