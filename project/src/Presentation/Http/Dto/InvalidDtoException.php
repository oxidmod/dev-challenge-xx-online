<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto;

use InvalidArgumentException;
use JsonSerializable;

class InvalidDtoException extends InvalidArgumentException implements JsonSerializable
{
    private array $errors = [];

    public function __construct()
    {
        parent::__construct('Invalid data was given.');
    }

    public function addError(string $field, string $error): self
    {
        $this->errors[$field][] = $error;
        return $this;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function jsonSerialize(): mixed
    {
        return $this->errors;
    }
}
