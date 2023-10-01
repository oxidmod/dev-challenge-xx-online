<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto;

class SaveSheetRequestDto extends SheetRequestDto
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

        $this->value = (string)$data['value'];
    }
}
