<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new Zend\Soap\Client('http://localhost/learn/soap/hello?wsdl');
$result = $client->sayHello(['firstName' => 'World']);

echo $result->sayHelloResult;