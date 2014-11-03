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
	
	/** @var string */
	private $holidayYearStart;
	
	/**  @var DateTimeFactory */
	private $dateFactory;
	
	/** @var ReachIterator */
	private $iterator;
	
	/** @var integer */
	private $borderYearsNumber;



	public function __construct(Settings $settings, DateTimeFactory $dateFactory, ReachIterator $iterator)
	{
		$this->credits = $settings->get('holiday.credits');
		$this->holidayYearStart = $settings->get('holiday.yearStart');
		$this->dateFactory = $dateFactory;
		$this->iterator = $iterator->setArray($this->credits);
	}

	
	public function getFormSelection()
	{
		$selection = [];
		
		$borderYearsNumber = $this->getBorderYearsNumber();
		
		$selection['full'] = "More than " . $this->pluralizeYear($borderYearsNumber);
		
		$selection['date'] = "Less than " . $this->pluralizeYear($borderYearsNumber);

		return $selection;
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
	
	public function getUserCredits(\Nette\Security\User $user, UserFacadeFactory $userFacadeFactory, BankHolidayFacadeFactory $bankholidayFacadeFactory)
	{
		if (!$user->isLoggedIn())
		{
			return 0;
		}
		
		$identity = $user->getIdentity();
		
		$userFacade = $userFacadeFactory->create();
		$bankholidayFacade = $bankholidayFacadeFactory->create();		
		
		list($type, $value) = explode(':', $identity->credits);
		
		$holidayYearStartDate = $this->dateFactory->create()->setTime(0, 0, 0);
		$dateString = $holidayYearStartDate->format('Y') . '-' . $this->holidayYearStart;
		$holidayYearStartDate->modify($dateString);
		
		$holidayYearEndDate = $holidayYearStartDate->cloneMe()->addYear();
		
		$bankHolidayCredits = count($bankholidayFacade->bankHolidays($holidayYearStartDate, $holidayYearEndDate));		
		
		if ($value === 'full')
		{
			return (int) end($this->credits) + $bankHolidayCredits;
		}		
		
		$employmentStart = $this->dateFactory->create($value)->setTime(0, 0, 0);
		
		$diff = $employmentStart->diff($holidayYearStartDate)->format('%y');
		
		foreach ($this->iterator as $yearsNumber => $credits)
		{
			$yearsNumberNext = $this->iterator->reachNextKey();
			
			if ($this->iterator->isLast())
			{
				if ($diff >= $yearsNumber)
				{
					return (int) $credits + $bankHolidayCredits;
				}
			}
			
			if ($diff >= $yearsNumber && $diff < $yearsNumberNext)
			{
				return (int) $credits  + $bankHolidayCredits;
			}
		}
	}
}
