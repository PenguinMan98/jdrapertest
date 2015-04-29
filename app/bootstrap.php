<?php

echo "This is the jdraper test<br>";

// Autoloader
spl_autoload(function($class){
	if(file_exists('Model/Data/'.$class.'.php'))
	{
		include('Model/Data/'.$class.'.php');
	}
	if(file_exists('Model/Structure/'.$class.'.php'))
	{
		include('Model/Structure/'.$class.'.php');
	}
});

