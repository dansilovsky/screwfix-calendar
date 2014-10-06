<?php
namespace Tests;

$configurator = require __DIR__ . '/../../../bootstrap.php';

use \Mockery as m;
use Tester\Assert;

class HolidayFacadeTest extends \Tester\TestCase {
	
	/** @var \Screwfix\HolidayFacade */
	private $obj;
	
	/** @var \Mockery\MockInterface */
	private $mCache;
	
	/** @var \Mockery\MockInterface */
	private $mCalendarDateTime;
	
	/** @var \Mockery\ExpectationInterface */
	private $mERepository;
	
	public function setUp()
	{	
		$this->mERepository = m::mock('Screwfix\HolidayRepository')->shouldReceive('getContext');
		$this->mCache = m::mock('Screwfix\Cache');
		$this->mCalendarDateTime = m::mock('Screwfix\CalendarDateTime');
	}
	
	public function tearDown()
	{
		m::close();
	}
	
	public function testGetHolidays()
	{
		$mSelection = Helpers::getMockRepoIterator(['date', 'halfday'], 
			[['2014-10-06', '0'], ['2014-10-07', '1'], ['2014-10-08', '0'], ['2014-10-09', '0']])
			->getMock();
		
		$mRepository = $this->mERepository->shouldReceive('between')->once()->andReturn($mSelection)->getMock();
		
		$this->obj = new \Screwfix\HolidayFacade($mRepository, $this->mCache, $this->mCalendarDateTime);
		
		$mFrom = m::mock('Screwfix\CalendarDateTime')->shouldReceive('format')->once()->getMock();
		$mTo = m::mock('Screwfix\CalendarDateTime')->shouldReceive('format')->once()->getMock();		
		
		$result = $this->obj->getHolidays(1, $mFrom, $mTo);
		
		$expected = [
			'2014-10-06' => 0,
			'2014-10-07' => 1,
			'2014-10-08' => 0,
			'2014-10-09' => 0
		];
		
		Assert::same($expected, $result);
	}
	
	public function testGetHolidays_noHolidaysFound()
	{
		$mSelection = Helpers::getMockRepoEmptyIterator()->getMock();
		
		$mRepository = $this->mERepository->shouldReceive('between')->once()->andReturn($mSelection)->getMock();
		
		$this->obj = new \Screwfix\HolidayFacade($mRepository, $this->mCache, $this->mCalendarDateTime);
		
		$mFrom = m::mock('Screwfix\CalendarDateTime')->shouldReceive('format')->once()->getMock();
		$mTo = m::mock('Screwfix\CalendarDateTime')->shouldReceive('format')->once()->getMock();		
		
		$result = $this->obj->getHolidays(1, $mFrom, $mTo);
		
		$expected = [];
		
		Assert::same($expected, $result);
	}
	
	public function testUpdateHolidays_deleteOne()
	{
		$mRepoChained = m::mock('Screwfix\HolidayRepository')->shouldReceive('delete')->once()->getMock();
		
		$mRepository = $this->mERepository->shouldReceive('getByDateUser')->with('2014-01-01', 1)->once()->andReturn($mRepoChained)->getMock();
		
		$this->obj = new \Screwfix\HolidayFacade($mRepository, $this->mCache, $this->mCalendarDateTime);
		
		$holidays = [['id' => '2014-01-01', 'holiday' => null]];
		
		$this->obj->updateHolidays(1, $holidays);
	}
	
	public function testUpdateHolidays_deleteMoreThanOne()
	{
		$mRepoChained = m::mock('Screwfix\HolidayRepository')->shouldReceive('delete')->once()->getMock();
		
		$mRepository = $this->mERepository->shouldReceive('between')->with(1, '2014-01-01', '2014-01-03')->once()->andReturn($mRepoChained)->getMock();
		
		$this->obj = new \Screwfix\HolidayFacade($mRepository, $this->mCache, $this->mCalendarDateTime);
		
		$holidays = [
			['id' => '2014-01-01', 'holiday' => null],
			['id' => '2014-01-02', 'holiday' => null],
			['id' => '2014-01-03', 'holiday' => null],
		];
		
		$this->obj->updateHolidays(1, $holidays);
	}
	
	public function testUpdateHolidays_add()
	{
		$mContext = m::mock('Nette\Database\Context')
			->shouldReceive('beginTransaction')->once()
			->shouldReceive('commit')->once()
			->getMock()
			;
		
		$mRepo = m::mock('Screwfix\HolidayRepository')->shouldReceive('getContext')->andReturn($mContext)
			->shouldReceive('save')->once()->with('2014-01-01', 0, 1)
			->shouldReceive('save')->once()->with('2014-01-02', 0, 1)
			->shouldReceive('save')->once()->with('2014-01-03', 0, 1)			
			->getMock()
			;
		
		$obj = new \Screwfix\HolidayFacade($mRepo, $this->mCache, $this->mCalendarDateTime);
		
		$holidays = [
			['id' => '2014-01-01', 'holiday' => 0],
			['id' => '2014-01-02', 'holiday' => 0],
			['id' => '2014-01-03', 'holiday' => 0],
		];
		
		$obj->updateHolidays(1, $holidays);
	}
	
	public function testGetDebits()
	{		
		$mSelection = Helpers::getMockRepoIterator(['halfday'], [[0], [0], [0], [1]])->getMock();
		
		$mRepository = m::mock('Screwfix\HolidayRepository')
			->shouldReceive('getContext')
			->shouldReceive('between')->once()->andReturn($mSelection)->getMock();
		
		$this->obj = new \Screwfix\HolidayFacade($mRepository, $this->mCache, $this->mCalendarDateTime);
		
		$result = $this->obj->getDebits(1, '2014-04-01', '2015-03-31');
		
		Assert::same(3.5, $result);
	}	
}

$test = new HolidayFacadeTest($configurator);
$test->run();
