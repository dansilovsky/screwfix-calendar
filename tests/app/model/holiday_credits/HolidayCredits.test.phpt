<?php

namespace Tests;

$container = require __DIR__ . '/../../../bootstrap.php';

use \Mockery as m;
use Tester\Assert;
use Screwfix\HolidayCredits;


class HolidayCreditsTest extends \Tester\TestCase {
	
	function tearDown()
	{
		\Mockery::close();
	}

	function testGetFormSelection()
	{
		$mSettings = m::mock('Screwfix\Settings')->shouldReceive('get')->with('holiday.credits')->andReturn([0 => 23, 2 => 24, 4 => 25, 6 => 26])->getMock();
		$mDateFactory = m::mock('Screwfix\DateTimeFactory');

		$obj = new HolidayCredits($mSettings, $mDateFactory);

		$expected = [
			'full' => 'More than 7 years',
			'date' => 'Less than 7 years'
		];
		Assert::same($expected, $obj->getFormSelection());
	}
	
	function testGetBorderDate()
	{
		$mSettings = m::mock('Screwfix\Settings')->shouldReceive('get')->with('holiday.credits')->andReturn([0 => 23, 2 => 24, 4 => 25, 6 => 26])->getMock();
		$mDate = m::mock('Screwfix\DateTime')
			->shouldReceive('subYear')->once()->with(7)
			->shouldReceive('toString')->once()
			->getMock()
		;		
		$mDateFactory = m::mock('Screwfix\DateTimeFactory')->shouldReceive('create')->once()->andReturn($mDate)->getMock();

		$obj = new HolidayCredits($mSettings, $mDateFactory);

		// just run method getBorderDate()
		$obj->getBorderDate();
	}
	
	function testGetBorderYearsNumber()
	{
		$mSettings = m::mock('Screwfix\Settings')->shouldReceive('get')->with('holiday.credits')->andReturn([0 => 23, 2 => 24, 4 => 25, 6 => 26])->getMock();
		$mDateFactory = m::mock('Screwfix\DateTimeFactory');
		
		$obj = new HolidayCredits($mSettings, $mDateFactory);
		
		Assert::same(7, $obj->getBorderYearsNumber());
	}

}

$test = new HolidayCreditsTest();
$test->run();