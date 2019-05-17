# Implementing a SOAP API with PHP 7

## Table of contents

* [Intro](#intro)
* [Creating a SOAP Endpoint](#creating-a-soap-endpoint)
* [Creating a SOAP Client](#creating-a-soap-client)
* [Creating a SOAP Client with C#](#creating-a-soap-client-with-c)

## Intro

It's 2017/2018 and you're probably wondering why I'm blogging about this topic, aren't you? I'll tell you a secret: XML is not dead. No, it's more important and necessary than ever. 

> "XML is crap. Really. There are no excuses. XML is nasty to parse for humans, and it's a disaster to parse even for computers. There's just no reason for that horrible crap to exist." Linus Torvalds

Just because it comes from [Mr Torvalds does not necessarily make it right](http://www.kidstrythisathome.com/2014/03/why-linus-torvalds-is-wrong-about-xml/).

And the question is what to use instead. Any self-developed file format? HTML? When XML is so similar and that's even more chaotic in practice? JSON? 

XML is really great, because it is the first time that a good standard has been set up to exchange data in a structured, machine-readable and validatable manner with minimal issues.

All this is missing from JSON - which still has its excellent use cases.

Where is the validation capability at JSON? How to write comments?

Moreover, JSON is often more chatty than XML, and even ALWAYS more chatty than countless other transmission methods. Also, its self-descriptiveness often leaves a lot to be desired, and it suffers from the fact that it was not as excellently written down as XML was. There are hundreds of libs, and quite a few interpret things completely differently than others - a good Esperanto looks different.

Nothing against JSON (I like to use it), but turning down XML this way is really stupid, dear Linus.

## Creating a SOAP Endpoint

The endpoint is the URL where your service can be accessed by a client application. To see the [WSDL](https://en.wikipedia.org/wiki/Web_Services_Description_Language) you add `?wsdl` to the web service endpoint URL.

### Requiremnts

While in. NET it's very simple, in PHP you need some extras. However, these are very easy to install.

* PHP 7+
* Enable `extension=php_soap.dll` in your `php.ini` file
* [zend-soap](https://docs.zendframework.com/zend-soap/)

I choose the Zend SOAP library because it's compatible with .NET clients.

### Installation

Install the zend-soap library:

```
composer require zendframework/zend-soap
```

Create a php file that acts as a SOAP endpoint. As filename I choose `server.php`.

Let's create a simple hello world webservice.

Content of `server.php`:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

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

$serverUrl = "http://localhost/hello/server.php";
$options = [
    'uri' => $serverUrl,
];
$server = new Zend\Soap\Server(null, $options);

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
```

Open the browser to see the WSDL file: http://localhost/hello/server.php?wsdl

## Creating a SOAP Client

Create a new file `client.php`. 

File content:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$client = new Zend\Soap\Client('http://localhost/hello/server.php?wsdl');
$result = $client->sayHello(['firstName' => 'World']);

echo $result->sayHelloResult;
```

The result (response) should look like this:

```
Hello World
```

As result you will receive a stdClass instance:

```php
var_dump($result);
```
```php
class stdClass#4 (1) {
  public $sayHelloResult =>
  string(11) "Hello World"
}
```

It is possible that your PHP Static Analysis Tool (e.g. phpstan) has a problem with calling `$client->sayHello(...)`. The reason is that this method does not exist and is called internally via a magic method. To avoid this warning there is a simple trick. Instead, call the web service methods using `call(method, params)`-methode and simply pass the method name as a string. Note that the parameters must be passed in a double nested array.

This is the **wrong** way to use the `call` method:

```
$result = $client->call('sayHello', ['firstName' => 'World']);
```

This example leads to this error:

> Fatal error: Uncaught SoapFault exception: [env:Receiver] Too few arguments 
> to function Hello::sayHello(), 0 passed and exactly 1 expected in 
> zendframework\zend-soap\src\Client.php on line 1166

This is the **right** way to use the `call` method:

```php
$result = $client->call('sayHello', [['firstName' => 'World']]);
```

## Creating a SOAP Client with C#

Now, the simplest way is to generate proxy classes in C# application (this process is called adding service reference). There are 2 main way of doing this, .NET provides ASP.NET services, which is old way of doing SOA, and WCF, which is the latest framework from MS and provides many protocols, including open and MS proprietery ones.

Now, enough theory and lets do it step by step.

1. Open your project (or create a new one) in visual studio
2. Right click on the project (on the project and not the solution) in Solution Explorer and click `Add Service Reference`
3. A dialog should appear (Add service reference). 
  Enter the url (`http://localhost/hello/server.php?wsdl`) of your wsdl file and click the `Go` button.
4. Now you should see a `HelloService` (name may vary). You should see generated proxy class name and namespace. In my case, the namespace is `WindowsFormsApp1.ServiceReference1`, the name of proxy class is `HelloPortClient`. As I said above, class names may vary in your case. 
5. Go to your C# source code. Add `using WindowsFormsApp1.ServiceReference1`.
6. Now you can call the service this way:

```csharp
ServiceReference1.HelloPortClient helloClient = new ServiceReference1.HelloPortClient();

string result = helloClient.sayHello("World");

Console.WriteLine(result);
```

[Source](https://stackoverflow.com/questions/3100458/soap-client-in-net-references-or-examples)

Hope this helps, if you'll encounter any problem, let me know.

And now have fun creating your own SOAP webservice :-)