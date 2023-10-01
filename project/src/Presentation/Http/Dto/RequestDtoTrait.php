<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto;

trait RequestDtoTrait
{
    private InvalidDtoException $exception;

    protected function getException(): InvalidDtoException
    {
        if (!isset($this->exception)) {
            $this->exception = new InvalidDtoException();
        }

        return $this->exception;
    }
}
