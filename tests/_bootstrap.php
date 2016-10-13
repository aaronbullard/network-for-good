<?php
// This is global bootstrap for autoloading
require 'vendor/autoload.php';

function dd($string)
{
	var_dump($string);die;
}


$Loader = new josegonzalez\Dotenv\Loader(__DIR__ . '/../.env');
// Parse the .env file
$Loader->parse();
// Send the parsed .env file to the $_ENV variable
$Loader->toEnv();
