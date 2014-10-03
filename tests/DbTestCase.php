<?php

namespace Tests;

class DbTestCase extends \Tester\TestCase {

	/** @var \Nette\Configurator */
	protected $configurator;

	/** @var \Nette\DI\Container */
	protected $container;

	public function __construct(\Nette\Configurator $configurator)
	{
		$this->configurator = $configurator;
	}

	/**
	 * Db initialization must be done on separate container in case some servises are build from db sources.
	 * After db is initialized create new container that is safe to use.
	 * @param string $path path to initialization file
	 * @param string $name filename without suffix
	 */
	public function prepare($path, $name = null)
	{
		$fileName = $name === null ? 'initialization' : $name;

		$tempDb = $this->configurator->createContainer()
			->getService('nette.database.default.context');

		$tempDb->query(file_get_contents(TESTS_DIR . '/clear.sql'));

		$tempDb->query(file_get_contents($path . '/' . $fileName . '.sql'));

		$this->container = $this->configurator->createContainer();
	}
}
