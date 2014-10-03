<?php
/**
 * @skip
 */

namespace Tests;

$container = require __DIR__ . '/../../../bootstrap.php';

use \Mockery as m;
use Tester;
use Tester\Assert;

class SettingsFacadeTest extends \Tester\TestCase
{
	public function tearDown()
	{
		m::close();
	}
	
	public function test_helperSubBuild()
	{
		$mRepo = m::mock('Screwfix\SettingsRepository');
		$mCache = m::mock('Screwfix\Cache');
		$mDate = m::mock('Screwfix\CalendarDateTime');
		
		$obj = new \Screwfix\SettingsFacade($mRepo, $mCache, $mDate);
		
		Assert::same(array('master' => array('slave' => array('tom' => 'Tom'))), $obj->helperSubBuild('master.slave.tom', 's:3:"Tom";'));
	}
	
	public function test_helperSubBuild_singlIndexPath()
	{
		$mRepo = m::mock('Screwfix\SettingsRepository');
		$mCache = m::mock('Screwfix\Cache');
		$mDate = m::mock('Screwfix\CalendarDateTime');
		
		$obj = new \Screwfix\SettingsFacade($mRepo, $mCache, $mDate);
		
		Assert::same(array('master' => 'Tom'), $obj->helperSubBuild('master', 's:3:"Tom";'));
	}

	public function test_getSettings()
	{		
		$mRepo = m::mock('Screwfix\SettingsRepository')
		->shouldReceive('rewind')
		->shouldReceive('valid')->times(5)->andReturn(true, true, true, true, false)
		->shouldReceive('current')->times(4)->andReturn(
			new ActiveRowMock('master.slave.dan', 's:3:"Dan";'),
			new ActiveRowMock('master.slave.tom', 's:3:"Tom";'),
			new ActiveRowMock('master.tim', 's:3:"Tim";'),
			new ActiveRowMock('items.count', 'i:10;')
		)
		->shouldReceive('next')->getMock()
		;
		
		$mCache = m::mock('Screwfix\Cache');
		$mDate = m::mock('Screwfix\CalendarDateTime');
		
		$obj = new \Screwfix\SettingsFacade($mRepo, $mCache, $mDate);
		
		$result = array(
			'master' => array(
				'slave' => array(
					'dan' => 'Dan',
					'tom' => 'Tom'
				),
				'tim' => 'Tim',
			),
			'items' => array(
				'count' => 10
			)
		);
		
		Assert::same($result, $obj->getSettings());
	}
	
	public function test_getSettings_oneItem()
	{		
		$mRepo = m::mock('Screwfix\SettingsRepository')
		->shouldReceive('rewind')
		->shouldReceive('valid')->times(2)->andReturn(true, false)
		->shouldReceive('current')->times(1)->andReturn(
			new ActiveRowMock('master', 's:3:"Dan";')
		)
		->shouldReceive('next')->getMock()
		;
		
		$mCache = m::mock('Screwfix\Cache');
		$mDate = m::mock('Screwfix\CalendarDateTime');
		
		$obj = new \Screwfix\SettingsFacade($mRepo, $mCache, $mDate);
		
		$result = array(
			'master' => 'Dan'
		);
		
		Assert::same($result, $obj->getSettings());
	}

}

$test = new SettingsFacadeTest($container);
$test->run();


class ActiveRowMock {
	
	public $id, $value;
	
	function __construct($id, $value)
	{
		$this->id = $id;
		$this->value = $value;
	}
}




