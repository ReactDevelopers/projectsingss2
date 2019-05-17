<?php 
	/*
	|--------------------------------------------------------------------------
	| Detect The Application Environment
	|--------------------------------------------------------------------------
	|
	| Laravel takes a dead simple approach to your application environments
	| so you can just specify a machine name for the host that matches a
	| given environment, then we will automatically detect it for you.
	|
	*/

	$env = $app->detectEnvironment(function(){
		$environmentPath = __DIR__.'/../.env';
		if (file_exists($environmentPath)){
	  		$setEnv = trim(file_get_contents($environmentPath));
	  		
	  		$dotenv = new Dotenv\Dotenv(__DIR__ . '/../', '.env.'.$setEnv); 
	  		$dotenv->overload();
		}
	});