<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Response;

use JsonSerializable;

abstract class AbstractJsonResponseDto implements JsonSerializable
{
    protected array $data = [];

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}
