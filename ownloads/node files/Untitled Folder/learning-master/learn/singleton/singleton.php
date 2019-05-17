<?php 
/**
Following the same pattern, you can create your own singletons and add any necessary functionality you want. The main trick here is that we have made the constructor private which is what disallows creation of multiple instances of singleton class.

It is important to point out that Singleton design pattern is considered anti-pattern (and more so in the world of PHP) because of following reasons:

Singletons create global state which is bad; they create tight-coupling
Singletons are essentially class-oriented not object-oriented
We cannot tell what dependencies it contains, it hides logic
Because you cannot create multiple instances, you cannot easily test them
PHP runs on shared nothing architecture which essentially means unlike other languages like Java/.Net, life of a PHP application is just one http request which is why singletons do not make much sense in PHP
Having said that, many argue that you can at least use singletons for single-nature things like an instance of database or logging (though it is matter of personal preference on how you design database or logging stuff) while others avoid singletons completely (especially TDD Worshipers).

That's all there is to a singleton.
*/

class MySingleton
{
    public static function getInstance()
    {
        static $instance;

        if (null === $instance) {
            $instance = new self();
        }

        return $instance;
    }

    // prevent creating multiple instances due to "private" constructor
    private function __construct(){}

    // prevent the instance from being cloned
    private function __clone(){}

    // prevent from being unserialized
    private function __wakeup(){}
}

$instance = MySingleton::getInstance();