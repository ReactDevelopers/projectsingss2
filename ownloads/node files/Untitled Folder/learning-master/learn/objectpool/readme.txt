https://sourcemaking.com/

Intro
The Object Pool design pattern is a pattern that I find myself using a lot when I want to achieve better performance for my apps, especially now that I am working on my own framework (DevlobPHP).

In this post, I will explain step by step how to implement the object pool pattern. It is not hard, it is maybe one of the easiest creational patterns.

First let’s understand the problem that the Object Pool design pattern solves.

The problem
Imagine that you have a couple of workers, working on the background, something like image manipulation or video processing. Now, in case you have a new job, then most probably, what you would do is to create a new worker to work on the new job, however, that is wrong.

By creating a new worker every time you have a new job, it means that you use a lot of resources and the performance of your app, is not the best!

Example
Let’s take YouTube as an example. Every time I upload a new video, I have to wait for YouTube to process the video and create multiple formats, like 720p and 1080p.

Thus, my idea is that YouTube uses workers on the background to create the formats and do any additional processing.

The solution
The solution is to keep track of occupied and free workers. If there is a free worker available, then there is no need to create a new worker.

Let’s code!
keep track of workers that are currently working.
keep track of free workers, that have been released.
get worker.
release a worker.
So, to keep track of occupied and free workers, we need to create two arrays.

<?php

class WorkerPool
{
    private $occupiedWorkers = [];
    private $freeWorkers = [];
}
Thus, point 1 and 2 are already completed.

Point 3 is a bit more complex. To get a worker we need to do some checks first. This is pretty much the bread of the Object Pool design pattern.

The idea is that, yes we want to create a new worker, BUT only if there are no free workers. Thus, for point 3, we need to check if there is any free worker available for us to do some work, if there is, then we pop (take) that worker from the free workers array, else, we create a new worker and push him to the occupied workers array.

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
This function completes point 3. If the free workers array is 0, then there are no available free workers, thus we create a new one.

However, if there is a free worker in the array, then we use the array_pop method to pop the element off the end of the array.

Then we have to insert that worker (it does not matter if the worker is new or existed) in the occupied workers array.

You might have noticed that we use a class called WorkerEntity, which does not exist yet. That class has a constructor which accepts an ID and a name.

To construct an ID, we get the total number of workers (free and occupied) and we add 1 to get the next unique ID.

For the name, include a new names array with a couple of names.

private $names = [ 'John', 'Erika', 'Alex', 'Marina', 'Jessica'];
Now, create a new WorkerEntity.php class. To be honest, Worker.php would make more sense, but this class already exists from PHP and it is kinda reserved, so the easiest solution is to name our class WorkerEntity and not Worker.

For the WorkerEntity class, simply create two private variables, id and name and use the constructor to initialise them. Also, create getters for id and name.

<?php

class User
{
    private $id;
    private $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
}
Last, but not least, create a whatYouDoing method and return a message.

public function whatYouDoing()
{
    return "I am working DUDE!";
}
Good, now let’s go back to the WorkerPool.php class and implement one more method.

public function release(WorkerEntity $worker)
{
    $id = $worker->getId();

    if (isset($this->occupiedWorkers[$id])) {
        unset($this->occupiedWorkers[$id]);

        $this->freeWorkers[$id] = $worker;
    }
}
This is the release method and as you can see, all it does is to accept a worker, get the ID of that worker and check if the worker is currently occupied, if he is, then we release the worker from work and set him free. That’s all!

Let’s run this
To run the code, create a new index.php file.

Autoload the classes, create a new pool and 2 workers.

<?php

function __autoload($classname) {
    include $classname.'.php';
}

$pool = new WorkerPool();

$worker1 = $pool->getWorker();
$worker2 = $pool->getWorker();

var_dump($pool);
To take a look at what var_dump() returns to us, run the script with this command from the terminal.

php index.php
Or run a php server with

php -S localhost:8000
Basically, you should have 2 occupied workers and 0 free workers, as you can see below.


Now, if I release worker1:

<?php

function __autoload($classname) {
    include $classname.'.php';
}

$pool = new WorkerPool();

$worker1 = $pool->getWorker();
$worker2 = $pool->getWorker();

$pool->release($worker1);
var_dump($pool);
You can see that we have 1 working worker and 1 free worker.


Let’s do 1 last test.

create 2 workers
find out the name of the first worker
release the first worker
create a variable $worker3 and call $pool->getWorker() again.
You will notice that when we print worker’s 3 name, we get the same name as worker1, the reason is because worker1 was released, so when we had to get a 3rd worker, we actually called worker1 again for worker, since he was already free and available.
<?php

function __autoload($classname) {
    include $classname.'.php';
}

$pool = new WorkerPool();

$worker1 = $pool->getWorker();
$worker2 = $pool->getWorker();

echo $worker1->getName() . PHP_EOL;

$pool->release($worker1);

$worker3 = $pool->getWorker();

echo $worker3->getName() . PHP_EOL;
No matter, how many times you reload, worker1 and worker3 are the same person.