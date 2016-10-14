<?php
// This is global bootstrap for autoloading
require 'vendor/autoload.php';

function dd($string)
{
	echo var_export($string, true);
	exit;
}


$Loader = new josegonzalez\Dotenv\Loader(__DIR__ . '/../.env');
// Parse the .env file
$Loader->parse();
// Send the parsed .env file to the $_ENV variable
$Loader->toEnv();
