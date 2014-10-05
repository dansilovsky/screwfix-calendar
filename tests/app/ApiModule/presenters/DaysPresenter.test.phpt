<?php

namespace Tests;

$container = require __DIR__ . '/../../../bootstrap.configurator.php';

use \Mockery as m;
use \Tester\Assert;

class DaysPresenterTest extends DbTestCase {
	
	/** Nette\Application\IPresenterFactory */
	private $presenterFactory;
	
	/** @var \ApiModule\DaysPresenter */
	private $presenter;

	public function setUp()
	{
		\Tester\Environment::lock('database', dirname(TEMP_DIR));
	}	
	
	protected function prepare($initializationFile, $name = null)
	{		
		parent::prepare(__DIR__, $initializationFile);
		
		$this->presenterFactory = $this->container->getByType('Nette\Application\IPresenterFactory');			
		
		$this->presenter = $this->presenterFactory->createPresenter('Api:Days');
	}
	
	private function createRequestHolidays()
	{
		return new \Nette\Application\Request('Api:Days', 'PATCH', ['action' => 'update']);
	}
	
	private function createRequestNotes($id)
	{
		return new \Nette\Application\Request('Api:Days', 'PATCH', ['action' => 'update', 'id' => $id]);
	}
	
	public function testActionUpdate_noJsonData()
	{		
		$this->prepare('initialization.actionUpdate.addNotes');
		
		$this->presenter->login('dans', 'dans');
		
		$request = $this->createRequestNotes('2014-10-01');
		
		$response = $this->presenter->run($request)->getPayload();		
		
		$expected = ['error' => 'Failed. No json data received.'];
		
		Assert::same($expected, $response);
	}
	
	public function testActionUpdate_addNotes_oneNoteInResponse()
	{		
		$data = ['note' => [
				'id' => null,
				'note' => 'Note 4'
			]
		];
		
		$this->prepare('initialization.actionUpdate.addNotes');
		
		$this->presenter->setTestJsonData($data);
		
		$this->presenter->login('dans', 'dans');
		
		$request = $this->createRequestNotes('2014-10-01');
		
		$response = $this->presenter->run($request)->getPayload();		
		
		$expected = ['note' => [
				0 => [
						'id' => 4,
						'note' => 'Note 4'
				]
			]
		];
		
		Assert::same($expected, $response);
	}
	
	public function testActionUpdate_addNotes_multipleNotesInResponse()
	{		
		$data = ['note' => [
				'id' => null,
				'note' => 'Note 4'
			]
		];
		
		$this->prepare('initialization.actionUpdate.addNotes');
		
		$this->presenter->setTestJsonData($data);
		
		$this->presenter->login('dans', 'dans');
		
		$request = $this->createRequestNotes('2014-09-01');
		
		$response = $this->presenter->run($request)->getPayload();
		
		$expected = ['note' => [
				0 => [
						'id' => 2,
						'note' => 'Note 2'
				],
				1 => [
						'id' => 3,
						'note' => 'Note 3'
				],
				2 => [
						'id' => 4,
						'note' => 'Note 4'
				]
			]
		];
		
		Assert::same($expected, $response);
	}
	
	public function testActionUpdate_addNotes_invalidRequest()
	{		
		$data = ['noteXXX' => [
				'id' => null,
				'note' => 'Note 4'
			]
		];
		
		$this->prepare('initialization.actionUpdate.addNotes');
		
		$this->presenter->setTestJsonData($data);
		
		$this->presenter->login('dans', 'dans');
		
		$request = $this->createRequestNotes('2014-09-01');
		
		$response = $this->presenter->run($request)->getPayload();
		
		$expected = ['error' => 'Failed. Invalid request.'];
		
		Assert::same($expected, $response);
	}
	
	public function testActionUpdate_updateHolidays_failedValidation_invalidData()
	{
		$this->prepare('initialization.actionUpdate.updateHolidays');
		
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
		
		$this->presenter->setTestJsonData($data);
		
		$this->presenter->login('dans', 'dans');
		
		$request = $this->createRequestHolidays();
		
		$response = $this->presenter->run($request)->getPayload();

		Assert::same(['error' => 'Failed. Invalid data.'], $response);
		
		
	}
	
	public function testActionUpdate_updateHolidays_failedValidation_holidaysFromDifferentHolidayYear()
	{
		$this->prepare('initialization.actionUpdate.updateHolidays');
		
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
		
		$this->presenter->setTestJsonData($data);
		
		$this->presenter->login('dans', 'dans');
		
		$request = $this->createRequestHolidays();
		
		$response = $this->presenter->run($request)->getPayload();

		Assert::same(['error' => 'Failed. Attempt to save holidays from different holiday years.'], $response);
	}
	
	public function testActionUpdate_updateHolidays_failedValidation_tooManyHolidays()
	{
		$this->prepare('initialization.actionUpdate.updateHolidays.tooManyHolidays');
		
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
		
		$this->presenter->setTestJsonData($data);
		
		$this->presenter->login('dans', 'dans');
		
		$request = $this->createRequestHolidays();
		
		$response = $this->presenter->run($request)->getPayload();

		Assert::same(['error' => 'Failed. Too many holidays.'], $response);
	}
	
	public function testActionUpdate_updateHolidays_aHolidayInDatabaseAlreadyExist()
	{
		$this->prepare('initialization.actionUpdate.updateHolidays');
		
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
		
		$this->presenter->setTestJsonData($data);
		
		$this->presenter->login('dans', 'dans');
		
		$request = $this->createRequestHolidays();
		
		$response = $this->presenter->run($request)->getPayload();		

		Assert::same(['error' => 'Failed. One of sent holidays is already in database.'], $response);
	}
}

$test = new DaysPresenterTest($container);
$test->run();
