<?php

namespace Screwfix;

/**
 * Description of BankHolidayRepositoryFactory
 *
 * @author Daniel Silovsky
 */
class BankHolidayRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new BankHolidayRepository($this->context);
	}
}
