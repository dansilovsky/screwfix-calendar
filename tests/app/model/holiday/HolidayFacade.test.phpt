<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$container = require 'c:/dev/www/calendar/tests/bootstrap.php';

use \Mockery as m;
use Tester\Assert;

class HolidayFacadeTest extends Tester\TestCase {

	/** @var \Screwfix\HolidayFacade */
	private $_obj;
	
	/** @var \Nette\DI\Container */
	private $_container;
	
	/** @var \Nette\Database\Context */
	private $_db;
	
	public function __construct(\Nette\DI\Container $container)
	{
		$this->_container = $container;
	}
	
	public function setUp()
	{
		$this->_db = $this->_container->getService('nette.database.default.context');		
		$this->_db->query(file_get_contents(__DIR__ . '/initialization.sql'));
		
		$holidayRepository = $this->_container->getService('holidayRepository');		
		$mCache = m::mock('Screwfix\Cache');
		$mCalendarDateTime = m::mock('Screwfix\CalendarDateTime');
		
		$this->_obj = new \Screwfix\HolidayFacade($holidayRepository, $mCache, $mCalendarDateTime);
	}


	public function testGetDebits()
	{
		$result = $this->_obj->getDebits(1, '2014-04-01', '2015-03-31');
		
		Assert::same(2.5, $result);
	}
}

$test = new HolidayFacadeTest($container);
$test->run();
