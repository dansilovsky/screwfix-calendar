<?php
namespace Tests;

$configurator = require __DIR__ . '/../../../bootstrap.php';

use \Mockery as m;
use Tester\Assert;

class HolidayFacadeTest extends \Tester\TestCase {
	
	/** @var \Screwfix\HolidayFacade */
	private $obj;
	
	/** @var \Mockery\MockInterface **/
	private $mCache;
	
	/** @var \Mockery\MockInterface **/
	private $mCalendarDateTime;
	
	public function setUp()
	{	
		$this->mRepository = m::mock('Screwfix\HolidayRepository')->shouldReceive('getContext');
		$this->mCache = m::mock('Screwfix\Cache');
		$this->mCalendarDateTime = m::mock('Screwfix\CalendarDateTime');
	}
	
	public function testGetDebits()
	{		
		$mSelection = Helpers::getRepositoryMock(['halfday'], [[0], [0], [0], [1]]);
		
		$this->mRepository = m::mock('Screwfix\HolidayRepository')
			->shouldReceive('getContext')
			->shouldReceive('between')->once()->andReturn($mSelection)->getMock();
		
		$this->obj = new \Screwfix\HolidayFacade($this->mRepository, $this->mCache, $this->mCalendarDateTime);
		
		$result = $this->obj->getDebits(1, '2014-04-01', '2015-03-31');
		
		Assert::same(3.5, $result);
	}	
}

$test = new HolidayFacadeTest($configurator);
$test->run();
