<?php

include __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Config\Configurator;

//$configurator->setDebugMode(TRUE);  // debug mode MUST NOT be enabled on production server

$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)	
	->register();

$configurator->addConfig(__DIR__ . '/config/common.neon', $configurator::AUTO);

$localConfig = __DIR__ . '/config/local.neon';

if(file_exists($localConfig))
  $configurator->addConfig($localConfig, $configurator::DEVELOPMENT); // none section

$container = $configurator->createContainer();

return $container;
