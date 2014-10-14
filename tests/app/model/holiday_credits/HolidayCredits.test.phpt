<?php

$container = require __DIR__ . '/../../../bootstrap.php';

use \Mockery as m;
use Tester\Assert;
use Screwfix\HolidayCredits;

// mocks following iterators array [0 =>23, 1 => 24, 2 => 25]
$mReverseIterator = m::mock('Dan\ReverseReachIterator')
	->shouldReceive('setArray')
	->shouldReceive('rewind')
	->shouldReceive('valid')->times(4)->andReturnValues([true, true, true, false])
	->shouldReceive('current')->times(3)->andReturnValues([25, 24, 23])
	->shouldReceive('key')->times(3)->andReturnValues([2, 1, 0])
	->shouldReceive('isFirst')->times(3)->andReturnValues([false, false, true])
	->shouldReceive('isLast')->times(3)->andReturnValues([true, false, false])
	->shouldReceive('reachNext')->twice()->andReturnValues([2, 1])
	->shouldReceive('next')
	->getMock()
	;

$mSettings = m::mock('Screwfix\Settings')->shouldReceive('get')->with('holiday.credits')->andReturn([])->getMock();

$obj = new HolidayCredits($mReverseIterator, $mSettings);

$expected = [
	2 => "More than 2 years",
	1 => "Less than 2 years and more than 1 year",
	0 => "Less than 1 year"
];
Assert::same($expected, $obj->getFormSelection());

// mocks following iterators array [0 => 23, 2 => 24, 4 => 25, 6 => 26]
$mReverseIterator = m::mock('Dan\ReverseReachIterator')
	->shouldReceive('setArray')
	->shouldReceive('rewind')
	->shouldReceive('valid')->times(5)->andReturnValues([true, true, true, true, false])
	->shouldReceive('current')
	->shouldReceive('key')->times(4)->andReturnValues([6, 4, 2, 0])
	->shouldReceive('isFirst')->times(3)->andReturnValues([false, false, false, true])
	->shouldReceive('isLast')->times(3)->andReturnValues([true, false, false, false])
	->shouldReceive('reachNext')->times(3)->andReturnValues([6, 4, 2])
	->shouldReceive('next')
	->getMock()
	;

$mSettings = m::mock('Screwfix\Settings')->shouldReceive('get')->with('holiday.credits')->andReturn([])->getMock();

$obj = new HolidayCredits($mReverseIterator, $mSettings);

$expected = [
	6 => "More than 6 years",
	4 => "Less than 6 years and more than 4 years",
	2 => "Less than 4 years and more than 2 years",
	0 => "Less than 2 years"
];
Assert::same($expected, $obj->getFormSelection());
