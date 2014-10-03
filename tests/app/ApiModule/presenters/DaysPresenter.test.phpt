<?php

namespace Tests;

$container = require __DIR__ . '/../../../bootstrap.configurator.php';

use \Mockery as m;
use \Tester\Assert;

class DaysPresenterTest extends DbTestCase {
	
	/** Nette\Application\IPresenterFactory */
	private $_presenterFactory;
	
	/** @var \ApiModule\DaysPresenter */
	private $_presenter;
	
	private $_requestActionUpdate;

	public function setUp()
	{
		\Tester\Environment::lock('database', dirname(TEMP_DIR));
		
		$this->_requestActionUpdate = $this->_createRequest('PATCH', array('action' => 'update'));
	}	
	
	private function prepare($initializationFile)
	{		
		$this->initialize(__DIR__, $initializationFile);
		
		$this->_presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');			
		
		$this->_presenter = $this->_presenterFactory->createPresenter('Api:Days');
	}
	
	private function _createRequest($method, array $action)
	{
		return new \Nette\Application\Request('Api:Days', $method, $action);
	}
	
	public function testActionUpdate_addHolidays_failedValidation_invalidData()
	{
		$this->prepare('initialization.actionUpdate.addHolidays');
		
		$data = array(
			0 =>
			array(
				'id' => '{alert()}',
				'holiday' => 1,
			),
			1 =>
			array(
				'id' => '2014-11-04',
				'holiday' => 0,
			),
			2 =>
			array(
				'id' => '2014-11-05',
				'holiday' => 0,
			),
		);
		
		$this->_presenter->setTestJsonData($data);
		
		$this->_presenter->login('dans', 'dans');
		
		$response = $this->_presenter->run($this->_requestActionUpdate)->getPayload();

		Assert::same(['error' => 'Failed validation. Invalid data.'], $response);
		
		
	}
	
	public function testActionUpdate_addHolidays_failedValidation_holidaysFromDifferentHolidayYear()
	{
		$this->prepare('initialization.actionUpdate.addHolidays');
		
		$data = array(
			0 =>
			array(
				'id' => '2015-03-30',
				'holiday' => 1,
			),
			1 =>
			array(
				'id' => '2014-11-31',
				'holiday' => 0,
			),
			2 =>
			array(
				'id' => '2015-04-01',
				'holiday' => 0,
			),
		);
		
		$this->_presenter->setTestJsonData($data);
		
		$this->_presenter->login('dans', 'dans');
		
		$response = $this->_presenter->run($this->_requestActionUpdate)->getPayload();

		Assert::same(['error' => 'Failed validation. Attempt to save holidays from different holiday years.'], $response);
	}
	
	public function testActionUpdate_addHolidays_failedValidation_tooManyHolidays()
	{
		$this->prepare('initialization.actionUpdate.addHolidays.tooManyHolidays');
		
		$data = array(
			0 =>
			array(
				'id' => '2014-09-01',
				'holiday' => 0,
			),
			1 =>
			array(
				'id' => '2014-09-02',
				'holiday' => 0,
			),
			2 =>
			array(
				'id' => '2014-09-03',
				'holiday' => 0,
			),
			3 =>
			array(
				'id' => '2014-09-04',
				'holiday' => 0,
			),
		);
		
		$this->_presenter->setTestJsonData($data);
		
		$this->_presenter->login('dans', 'dans');		
		
		$response = $this->_presenter->run($this->_requestActionUpdate)->getPayload();

		Assert::same(['error' => 'Failed validation. Too many holidays.'], $response);
	}
	
	public function testActionUpdate_addHolidays_aHolidayInDatabaseAlreadyExist()
	{
		$this->prepare('initialization.actionUpdate.addHolidays');
		
		$data = array(
			0 =>
			array(
				'id' => '2014-09-06',
				'holiday' => 0,
			),
			1 =>
			array(
				'id' => '2014-09-07',
				'holiday' => 0,
			),
			2 =>
			array(
				'id' => '2014-09-09',
				'holiday' => 0,
			),
		);
		
		$this->_presenter->setTestJsonData($data);
		
		$this->_presenter->login('dans', 'dans');
		
		$response = $this->_presenter->run($this->_requestActionUpdate)->getPayload();
		
		

		Assert::same(['error' => 'Failed validation. One of sent holidays is already in database.'], $response);
	}
}

$test = new DaysPresenterTest($container);
$test->run();
