<?php

namespace Tests;

$container = require __DIR__ . '/../../../bootstrap.php';

use \Mockery as m;
use Tester;
use Tester\Assert;

class SettingsFacadeTest extends \Tester\TestCase
{
	/** @var \Mockery\MockInterface **/
	private $mRepo;
	
	/** @var \Mockery\MockInterface **/
	private $mCache;
	
	/** @var \Mockery\MockInterface **/
	private $mDate;
	
	public function setUp()
	{
		$this->mRepo = m::mock('Screwfix\SettingsRepository')->shouldReceive('getContext')->getMock();
		$this->mCache = m::mock('Screwfix\Cache');
		$this->mDate = m::mock('Screwfix\CalendarDateTime');		
	}

	public function tearDown()
	{
		m::close();
	}
	
	public function test_helperSubBuild()
	{
		$obj = new \Screwfix\SettingsFacade($this->mRepo, $this->mCache, $this->mDate);
		
		Assert::same(array('master' => array('slave' => array('tom' => 'Tom'))), $obj->helperSubBuild('master.slave.tom', 's:3:"Tom";'));
	}
	
	public function test_helperSubBuild_singlIndexPath()
	{
		$obj = new \Screwfix\SettingsFacade($this->mRepo, $this->mCache, $this->mDate);
		
		Assert::same(array('master' => 'Tom'), $obj->helperSubBuild('master', 's:3:"Tom";'));
	}

	public function test_getSettings()
	{		
		$mRepo = Helpers::getMockRepoIterator(['id', 'value'], [
				['master.slave.dan', 's:3:"Dan";'],
				['master.slave.tom', 's:3:"Tom";'],
				['master.tim', 's:3:"Tim";'],
				['items.count', 'i:10;']
			], 'Screwfix\SettingsRepository')
			->getMock()
			;
		
		$obj = new \Screwfix\SettingsFacade($mRepo, $this->mCache, $this->mDate);
		
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
		$mRepo = Helpers::getMockRepoIterator(['id', 'value'], [
				['master', 's:3:"Dan";']
			], 'Screwfix\SettingsRepository')
			->getMock()
			;
		
		$obj = new \Screwfix\SettingsFacade($mRepo, $this->mCache, $this->mDate);
		
		$result = ['master' => 'Dan'];
		
		Assert::same($result, $obj->getSettings());
	}

}

$test = new SettingsFacadeTest($container);
$test->run();




