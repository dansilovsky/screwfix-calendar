<?php

namespace Screwfix;

/**
 * HolidayRepositoryFactory
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class HolidayRepositoryFactory {
	
	private $_context;

	public function __construct(\Nette\Database\Context $context)
	{
		$this->_context = $context;
	}

	public function create()
	{
		return new HolidayRepository($this->_context);
	}
}
