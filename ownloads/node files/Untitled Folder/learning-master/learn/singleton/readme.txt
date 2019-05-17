https://sourcemaking.com/
https://sourcemaking.com/design_patterns/singleton/php/1


In the singleton pattern a class can distribute one instance of itself to other classes.

Singleton pattern is used for logging, drivers objects, caching and thread pool.

Suppose you created a simple Connection class to connect to a database, and you need to access database at multiple locations from your code, generally what you will do is create an instance of Connection class and use it for doing database operations wherever you require. This will result in creating multiple connections from database as each instance of Connection class will have a separate connection to database. In order to deal with it we create Connection class as singleton class, so that only one instance of Connection is created and single connection is established.

Singleton pattern restricts the instantiation of a class and ensures that only one instance of the class exists in the java virtual machine. The singleton class must provide a global access point to get the instance of the class. Singleton pattern is used for logging, drivers objects, caching and thread pool.


A singleton class is a class that has only one instance object. While not necessary for having a singleton, usually the class makes sure that it is only instantiated once. E.g. in Java you could use a private constructor and a static getInstacnce() method that returns the same instance again and again.

“Singleton” is a design pattern describing this behavior. Actually, by now it is actually considered an anti-pattern, because it makes writing unit tests more difficult, especially if the singleton class makes sure that there’s only one instance. Dependency injection (another design pattern) can be used to similar means, i.e. ensure that during regular run time there’s only one instance of a class - but objects will be given that instance, instead of fetching it themselves. And “be given” is controlled by something else, which can - or won’t - make sure that there’s only one instance.

What’s a database connection? 
It is an object that holds values required to maintain a connection to a database.
Connections are used for database queries, updates, transactions etc.

What is a singleton class in a database connection? 
Literally this asks for implementation details of database connections - whether a database connection uses a singleton class somehow. This is - without looking into the source code of the database driver.

Database connections treated as singletons - that is, somehow make sure your application only has one single database connection. It is not recommend, since that would be a big bottleneck, especially there are long running queries.



What "instantiated only once means?" It simply means that if an object of that class was already instantiated, the system will return it instead of creating new one. Why? Because, sometimes, you need a "common" instance (global one) or because instantiating a "copy" of an already existent object is useless.

Let's consider for first case a framework: on bootstrap operation you need to instantiate an object but you can (you have to) share it with other that request for a framework bootstrap.

For the second case let's consider a class that has only methods and no members (so basically no internal state). Maybe you could implement it as a static class, but if you want to follow design patterns, consider AbstractFactory) you should use objects. So, having some copy of the same object that has only methods isn't necessary and is also memory-wasting.

Those are two main reason to use singleton to me.

1. Singleton can be used like a global variable.

2. It can have only one instance (object) of that class unlike normal class.

3. When we don't want to create a more than one instance of a class like database connection or utility library we would go for singleton pattern.

4. Singleton makes sure you would never have more than one instance of a class.

5. Make a construct method private to make a class Singleton.

6. If you don't want to instantiate a multiple copies of class but only one then you just put it in singleton pattern and you can just call methods of that class and that class will have only one copy of it in a memory even if you create another instance of it.

7. If user just wants to connect to database then no need to create a another instance again if the instance already exist, you can just consume first object and make the connection happen.

e.g.

<?php
    class DBConn {

        private static $obj;

        private final function  __construct() {
            echo __CLASS__ . " initializes only once\n";
        }

        public static function getConn() {
            if(!isset(self::$obj)) {
                self::$obj = new DBConn();
            }
            return self::$obj;
        }
    }

    $obj1 = DBConn::getConn();
    $obj2 = DBConn::getConn();

    var_dump($obj1 == $obj2);
?>


When designing web applications, it often makes sense conceptually and architecturally to allow access to one and only one instance of a particular class. The singleton pattern enables us to do this.

<?php
class Singleton
{
    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }
}

class SingletonChild extends Singleton
{
}

$obj = Singleton::getInstance();
var_dump($obj === Singleton::getInstance());             // bool(true)

$anotherObj = SingletonChild::getInstance();
var_dump($anotherObj === Singleton::getInstance());      // bool(false)

var_dump($anotherObj === SingletonChild::getInstance()); // bool(true)
The code above implements the singleton pattern using a static variable and the static creation method getInstance(). Note the following:

The constructor __construct is declared as protected to prevent creating a new instance outside of the class via the new operator.
The magic method __clone is declared as private to prevent cloning of an instance of the class via the clone operator.
The magic method __wakeup is declared as private to prevent unserializing of an instance of the class via the global function unserialize().
A new instance is created via late static binding in the static creation method getInstance() with the keyword static. This allows the subclassing of the class Singleton in the example.
The singleton pattern is useful when we need to make sure we only have a single instance of a class for the entire request lifecycle in a web application. This typically occurs when we have global objects (such as a Configuration class) or a shared resource (such as an event queue).

You should be wary when using the singleton pattern, as by its very nature it introduces global state into your application, reducing testability. In most cases, dependency injection can (and should) be used in place of a singleton class. Using dependency injection means that we do not introduce unnecessary coupling into the design of our application, as the object using the shared or global resource requires no knowledge of a concretely defined class.