<?php
declare(strict_types=1);

namespace App\Infrastructure\Adapter\DependencyGraph;

use App\Domain\Sheet\CalculationException;
use App\Domain\Sheet\Cell;
use App\Domain\Sheet\DependencyGraphInterface;
use App\Domain\Sheet\Sheet;
use Digilist\DependencyGraph\CircularDependencyException;
use Digilist\DependencyGraph\DependencyGraph;
use Digilist\DependencyGraph\DependencyNode;

readonly class Graph implements DependencyGraphInterface
{
    private DependencyGraph $graph;

    private array $calculationOrder;

    public function __construct(Sheet $sheet, Cell $updatedCell)
    {
        $knownNodes = [];

        $graph = new DependencyGraph();
        foreach ($sheet->getCells() as $cell) {
            $cellNode = $this->addToKnownNodes($graph, $cell->getId(), $knownNodes);

            foreach ($cell->getReferencedCellIds() as $refId) {
                $refNode = $this->addToKnownNodes($graph, $refId, $knownNodes);

                $graph->addDependency($cellNode, $refNode);
            }
        }

        try {
            $this->graph = $graph;
            $this->calculationOrder = $graph->resolve();
        } catch (CircularDependencyException $exception) {
            throw CalculationException::circularReference($updatedCell, $exception);
        }
    }

    public function getCalculationOrder(): array
    {
        return $this->calculationOrder;
    }

    public function hasDependency(Cell $cell, Cell $fromCell): bool
    {
        foreach ($this->graph->getNodes() as $node) {
            if ($node->getElement() === $cell->getId()) {
                foreach ($node->getDependencies() as $dependencyNode) {
                    if ($dependencyNode->getElement() === $fromCell->getId()) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function addToKnownNodes(
        DependencyGraph $graph,
        string $cellId,
        array &$knownNodes
    ): DependencyNode {
        if (!array_key_exists($cellId, $knownNodes)) {
            $node = new DependencyNode($cellId);
            $graph->addNode($node);

            $knownNodes[$cellId] = $node;
        }

        return $knownNodes[$cellId];
    }
}
