<?php 

/**
Multiton
As the name suggests, a multiton is a design pattern that helps you create multiple instances of itself. Both singleton and multiton are same, the only difference is that a multiton can store and retrieve multiple instances of itself. Obviously multiton suffers from same problems as singletons.
*/

class Logger
{
    private static $instances = array();

    public static function getInstance($key)
    {
        if(!array_key_exists($key, self::$instances)) {
            self::$instances[$key] = new self();
        }

        return self::$instances[$key];
    }

    // prevent creating multiple instances due to "private" constructor
    private function __construct(){}

    // prevent the instance from being cloned
    private function __clone(){}

    // prevent from being unserialized
    private function __wakeup(){}
}

$firstInstance = Logger::getInstance('file');
$secondInstance = Logger::getInstance('email');

?>