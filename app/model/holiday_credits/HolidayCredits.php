<?php

namespace Screwfix;

use Dan\ReachIterator;
use Dan\Lang;

/**
 * Description of HolidayCredits
 *
 * @author Daniel Silovsky
 */
class HolidayCredits {

	/** @var array */
	private $credits;
	
	/**  @var DateTimeFactory */
	private $dateFactory;
	
	/** @var integer */
	private $borderYearsNumber;



	public function __construct(Settings $settings, DateTimeFactory $dateFactory)
	{
		$this->credits = $settings->get('holiday.credits');
		$this->dateFactory = $dateFactory;
	}

	
	public function getFormSelection()
	{
		$selection = [];
		
		$borderYearsNumber = $this->getBorderYearsNumber();
		
		$selection['full'] = "More than " . $this->pluralizeYear($borderYearsNumber);
		
		$selection['date'] = "Less than " . $this->pluralizeYear($borderYearsNumber);

		return $selection;
	}
	
	
	public function workOutPostCredits()
	{
		
	}
	

	private function pluralizeYear($number)
	{
		return Lang::pluralize('%d year', '%d years', $number);
	}
	
	/**
	 * @return string date in format yyyy-mm-dd
	 */
	public function getBorderDate()
	{
		$date = $this->dateFactory->create();
		
		$yearsCount = $this->getBorderYearsNumber();
		
		$date->subYear($yearsCount);
		
		return $date->toString();
	}
	
	/**
	 * @return integer
	 */
	public function getBorderYearsNumber()
	{
		if ($this->borderYearsNumber === null)
		{
			end($this->credits);
		
			$this->borderYearsNumber = key($this->credits) + 1;
		}
		
		return $this->borderYearsNumber;
		
	}
}
