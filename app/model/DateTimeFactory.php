<?php

namespace Screwfix;

/**
 * DateTimeFactory
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class DateTimeFactory {

	public function create($time = 'now')
	{
		return new DateTime($time);
	}
}
