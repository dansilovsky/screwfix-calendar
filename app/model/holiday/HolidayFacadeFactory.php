<?php

namespace Screwfix;

/**
 * HolidayFacadeFactory
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class HolidayFacadeFactory extends RepositoryFacadeFactory {
	
	public function __construct(HolidayRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}

}
