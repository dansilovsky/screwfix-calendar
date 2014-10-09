<?php

namespace Screwfix;

/**
 * HolidayRepositoryFactory
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class HolidayRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new HolidayRepository($this->context);
	}
}
