<?php

namespace Screwfix;
use \Nette\Utils\Arrays;

/**
 * SettingsFacade
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class SettingsFacade extends RepositoryFacade {

	public function __construct(SettingsRepository $repository, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repository, $cache, $date);
	}
	
	/**
	 * Builds array of settings from database and returns it.
	 * 
	 * @return array settings
	 */
	public function getSettings()
	{		
		$settings = [];
		
		foreach ($this->repository as $row)
		{
			$subSettings = $this->helperSubBuild($row->id, $row->value);
			
			$settings = Arrays::mergeTree($settings, $subSettings);			
		}
	
		return $settings;
	}
	
	/**
	 * Builds associative array from given path and value. 
	 * Value will be unserialized.
	 * 
	 * eg. path = 'master.slave.tom'
	 *     value = 's:3:"Tom";'
	 *     result = array('master' => array('slave' => array('tom' => 'Tom')))
	 * 
	 * @param string $path path separeted by periods
	 * @param string $value serialized value
	 * @return array
	 */
	public function helperSubBuild($path, $value)
	{
		$settings =[];
		
		$indexes = explode('.', $path, 2);
		
		if (count($indexes) > 1)
		{
			$settings[$indexes[0]] = $this->helperSubBuild($indexes[1], $value);
		}
		else
		{
			$settings[$indexes[0]] = unserialize($value);
		}
	
		return $settings;
	}
}
