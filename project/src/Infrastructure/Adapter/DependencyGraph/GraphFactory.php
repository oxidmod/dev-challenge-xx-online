<?php
declare(strict_types=1);

namespace App\Infrastructure\Adapter\DependencyGraph;

use App\Domain\Sheet\Cell;
use App\Domain\Sheet\DependencyGraphFactoryInterface;
use App\Domain\Sheet\DependencyGraphInterface;
use App\Domain\Sheet\Sheet;

class GraphFactory implements DependencyGraphFactoryInterface
{
    public function create(Sheet $sheet, Cell $updatedCell): DependencyGraphInterface
    {
        return new Graph($sheet, $updatedCell);
    }
}
