<?php

namespace Screwfix;

/**
 * Description of DateInputFactory
 *
 * @author Daniel Silovsky
 */
class EmploymentDateInputFactory {
	
	/** @var HolidayCredits */
	private $holidayCredits;
	
	public function __construct(HolidayCredits $holidayCredits)
	{
		$this->holidayCredits = $holidayCredits;
	}
	
	public function create()
	{
		return new EmploymentDateInput($this->holidayCredits);
	}
}
