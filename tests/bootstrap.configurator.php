<?php

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists('Tester\Assert')) {
	echo "Install Nette Tester using `composer update --dev`\n";
	exit(1);
}

Tester\Environment::setup();

@mkdir(__DIR__ . '/temp');  # @ - adresář již může existovat
define('TEMP_DIR', __DIR__ . '/temp/' . getmypid());
Tester\Helpers::purge(TEMP_DIR);

define('TESTS_DIR', __DIR__);

$configurator = new Nette\Configurator;
$configurator->setDebugMode(FALSE);
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__ . '/../app')
	->addDirectory(__DIR__ . '/../vendor')
	->addDirectory(__DIR__)
	->register();
$configurator->addConfig(__DIR__ . '/../app/config/config.neon', 'testing');
$configurator->addConfig(__DIR__ . '/../app/config/config.local.neon', $configurator::NONE);
return $configurator;