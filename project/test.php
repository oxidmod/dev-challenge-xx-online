<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$graph = new \Digilist\DependencyGraph\DependencyGraph();
$a = new \Digilist\DependencyGraph\DependencyNode('a');
$b = new \Digilist\DependencyGraph\DependencyNode('b');
$c = new \Digilist\DependencyGraph\DependencyNode('c');
$d = new \Digilist\DependencyGraph\DependencyNode('d');
$e = new \Digilist\DependencyGraph\DependencyNode('e');

$f = new \Digilist\DependencyGraph\DependencyNode('f');
$g = new \Digilist\DependencyGraph\DependencyNode('g');
$h = new \Digilist\DependencyGraph\DependencyNode('h');
$i = new \Digilist\DependencyGraph\DependencyNode('i');
$j = new \Digilist\DependencyGraph\DependencyNode('j');

$graph->addDependency($b, $a);
$graph->addDependency($c, $a);
$graph->addDependency($c, $b);
$graph->addDependency($d, $b);
$graph->addDependency($e, $c);

$graph->addDependency($g, $f);
$graph->addDependency($j, $f);
$graph->addDependency($h, $j);
$graph->addDependency($g, $i);

var_dump($graph->resolve());

$nodesToCalculate = [];
foreach ($graph->getNodes() as $node) {
    if (in_array($a, $node->getDependencies(), true)) {
//        echo sprintf('Node "%s" is dependent from node "%s"', $node->getElement(), $a->getElement()) . PHP_EOL;
        $nodesToCalculate[] = $node;
    }
    echo  $node->getElement() . "\t" . json_encode(array_map(fn(\Digilist\DependencyGraph\DependencyNode $q) => $q->getElement(), $node->getDependencies())) . PHP_EOL;
}

usort($nodesToCalculate, function (\Digilist\DependencyGraph\DependencyNode $a, \Digilist\DependencyGraph\DependencyNode $b) {
//    var_dump([
//        'a' => [$a->getElement(), count($a->getDependencies())],
//        'b' => [$b->getElement(), count($b->getDependencies())],
//    ]);
    return count($a->getDependencies()) <=> count($b->getDependencies());
});

var_dump(array_map(fn(\Digilist\DependencyGraph\DependencyNode $node) => $node->getElement(), $nodesToCalculate));