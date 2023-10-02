<?php
declare(strict_types=1);

namespace App\Presentation\Http\Dto\Request;

trait RequestDtoTrait
{
    private InvalidRequestDtoException $exception;

    protected function getException(): InvalidRequestDtoException
    {
        if (!isset($this->exception)) {
            $this->exception = new InvalidRequestDtoException();
        }

        return $this->exception;
    }
}
