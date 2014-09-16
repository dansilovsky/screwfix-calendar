<?php

namespace Screwfix;

/**
 * HolidayFacadeFactory
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class HolidayFacadeFactory {

	private $_holidayRepositoryFactory;
	
	private $_cache;
	
	private $_date;
	
	public function __construct(HolidayRepositoryFactory $holidayRepositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		$this->_holidayRepositoryFactory = $holidayRepositoryFactory;
		$this->_cache = $cache;
		$this->_date = $date;
	}
	
	public function create()
	{
		$repository = $this->_holidayRepositoryFactory->create();
		
		return new HolidayFacade($repository, $this->_cache, $this->_date);
	}
}
