<?php


class Hello
{
    /**
     * Say hello.
     *
     * @param string $firstName
     * @return string $greetings
     */
    public function sayHello($firstName)
    {
        return 'Hello ' . $firstName;
    }

}

$serverUrl = "http://localhost/learn/soap/hello/server.php";
$options = [
    'uri' => $serverUrl,
];
$server = new Zend\Soap\Server(null, $options);

/*echo '<pre>';
print_r($server);
echo '</pre>';*/

if (isset($_GET['wsdl'])) {
    $soapAutoDiscover = new \Zend\Soap\AutoDiscover(new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence());
    $soapAutoDiscover->setBindingStyle(array('style' => 'document'));
    $soapAutoDiscover->setOperationBodyStyle(array('use' => 'literal'));
    $soapAutoDiscover->setClass('Hello');
    $soapAutoDiscover->setUri($serverUrl);
    
    header("Content-Type: text/xml");
    echo $soapAutoDiscover->generate()->toXml();
} else {
    $soap = new \Zend\Soap\Server($serverUrl . '?wsdl');
    $soap->setObject(new \Zend\Soap\Server\DocumentLiteralWrapper(new Hello()));
    $soap->handle();
}

?>