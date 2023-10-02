<?php
declare(strict_types=1);

namespace App\Domain\Sheet;

use App\Domain\NotFoundException;

interface SheetsRepositoryInterface
{
    /**
     * @param string $sheetId
     * @return Sheet
     */
    public function getOrCreateSheet(string $sheetId): Sheet;

    /**
     * @param string $sheetId
     * @return Sheet
     *
     * @throws NotFoundException
     */
    public function getSheet(string $sheetId): Sheet;
}
