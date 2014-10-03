<?php
namespace Tests;

$configurator = require __DIR__ . '/../../../bootstrap.configurator.php';

use \Mockery as m;
use Tester\Assert;

class HolidayFacadeTest extends DbTestCase {

	/** @var \Screwfix\HolidayFacade */
	private $obj;
	
	public function setUp()
	{
		\Tester\Environment::lock('database', dirname(TEMP_DIR));
		
		$this->initialize(__DIR__);
		
		$holidayRepository = $this->container->getService('holidayRepository');		
		$mCache = m::mock('Screwfix\Cache');
		$mCalendarDateTime = m::mock('Screwfix\CalendarDateTime');
		
		$this->obj = new \Screwfix\HolidayFacade($holidayRepository, $mCache, $mCalendarDateTime);
	}


	public function testGetDebits()
	{
		$result = $this->obj->getDebits(1, '2014-04-01', '2015-03-31');
		
		Assert::same(10.5, $result);
	}
}

$test = new HolidayFacadeTest($configurator);
$test->run();
