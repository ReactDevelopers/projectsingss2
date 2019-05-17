<?php

class WorkerPool
{
    private $occupiedWorkers = [];
    private $freeWorkers = [];
    private $names = [ 'John', 'Erika', 'Alex', 'Marina', 'Jessica'];

    public function getWorker()
    {
        if (count($this->freeWorkers) == 0) {
            $id = count($this->occupiedWorkers) + count($this->freeWorkers) + 1;
            $randomName = array_rand($this->names, 1);

            $worker = new WorkerEntity($id, $this->names[$randomName]);
        } else
            $worker = array_pop($this->freeWorkers);

        $this->occupiedWorkers[$worker->getId()] = $worker;

        return $worker;
    }

    public function release(WorkerEntity $worker)
    {
        $id = $worker->getId();

        if (isset($this->occupiedWorkers[$id])) {
            unset($this->occupiedWorkers[$id]);

            $this->freeWorkers[$id] = $worker;
        }
    }
}