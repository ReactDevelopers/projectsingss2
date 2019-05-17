<?php

function __autoload($classname) {
    include $classname.'.php';
}

$pool = new WorkerPool();

$worker1 = $pool->getWorker();
$worker2 = $pool->getWorker();

echo $worker1->getName() . PHP_EOL;
echo $worker2->getName() . PHP_EOL;

$pool->release($worker1);

$worker3 = $pool->getWorker();

echo $worker3->getName() . PHP_EOL;
