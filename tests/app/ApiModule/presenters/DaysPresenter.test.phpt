<?php

$container = require 'c:/dev/www/calendar/tests/bootstrap.php';

use \Mockery as m;
use Tester\Assert;

// z DI kontejneru, který vytvořil bootstrap.php, získáme instanci PresenterFactory
//$presenterFactory = $container->getByType('Nette\Application\IPresenterFactory');
//$presenter = $presenterFactory->createPresenter('Api:Days');
//// add holidays - all data valid
//$presenter->setTestJsonData(array(
//	0 =>
//	array(
//		'id' => '2014-09-08',
//		'holiday' => 0,
//	),
//	1 =>
//	array(
//		'id' => '2014-09-09',
//		'holiday' => 0,
//	),
//	2 =>
//	array(
//		'id' => '2014-09-10',
//		'holiday' => 0,
//	),
//));
//
//$presenter->autoCanonicalize = false;
//
//$request = new Nette\Application\Request('Api:Days', 'PATCH', array('action' => 'update'));
//
//$response = (string) $presenter->run($request);
//
//Assert::same('[{"id":"2014-09-08","holiday":0},{"id":"2014-09-09","holiday":0}]', $response);


//class SettingTest extends TestCase {
//
//	private $container;
//	private $setting;
//
//	public function __construct(Container $container)
//	{
//		$this->container = $container;
//		$this->setting = new Setting($container);
//
//		$this->container->getService('database')->loadFile(__DIR__ . '/../initialization.sql');
//	}
//
//}

class DaysPresenterTest extends Tester\TestCase {
	/** @var \Nette\DI\Container */
	private $_container;
	
	/** @var \Nette\Database\Context */
	private $_db;
	
	/** @var \ApiModule\DaysPresenter */
	private $_presenter;
	
	private $_requestActionUpdate;
	
	public function __construct(\Nette\DI\Container $container)
	{
		$this->_container = $container;
		
		$this->_db = $this->_container->getService('nette.database.default.context');
		
		$presenterFactory = $this->_container->getByType('Nette\Application\IPresenterFactory');
		
		$this->_presenter = $presenterFactory->createPresenter('Api:Days');
	}
	
	private function prepareDb($initializationFile)
	{
		$this->_db->query(file_get_contents(__DIR__ . '/' . $initializationFile));
	}
	
	private function _createRequest($method, array $action)
	{
		return new Nette\Application\Request('Api:Days', $method, $action);
	}


	public function setUp()
	{
		# Příprava
		$this->_requestActionUpdate = $this->_createRequest('PATCH', array('action' => 'update'));
		
	}

	public function testActionUpdate_addHolidays()
	{
		$this->prepareDb('initialization.actionUpdate.addHolidays.sql');
		
		$data = array(
			0 =>
			array(
				'id' => '2014-09-08',
				'holiday' => 0,
			),
			1 =>
			array(
				'id' => '2014-09-09',
				'holiday' => 0,
			),
			2 =>
			array(
				'id' => '2014-09-10',
				'holiday' => 0,
			),
		);
		
		$this->_presenter->setTestJsonData($data);
		
		$this->_presenter->login('dans', 'dans');
		
		$response = $this->_presenter->run($this->_requestActionUpdate)->getPayload();
		\Nette\Diagnostics\Debugger::dump($response);
		exit;
		Assert::true(true);
	}
}

$test = new DaysPresenterTest($container);
$test->run();
