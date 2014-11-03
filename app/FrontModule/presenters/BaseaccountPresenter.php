<?php
namespace FrontModule;

use Nette\Application\UI\Form,
	Screwfix\BaseaccountForm;

/**
 * BaseaccountPresenter
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
abstract class BaseaccountPresenter extends BasePresenter {
	
	/** @var \Screwfix\PatternInputEditFactory @inject */
	public $patternInputEditFactory;
	
	/** @var \Screwfix\PatternInputOverviewFactory @inject */
	public $patternInputOverviewFactory;
	
	/** @var \Screwfix\EmploymentDateInputFactory @inject */
	public $employmentDateInputFactory;
	
	/** @var \Screwfix\ShiftPatternIteratorFactory @inject */
	public $patternIteratorFactory;
	
	/** @var \Screwfix\ShiftPatternFilterFactory @inject */
	public $shiftPatternFilterFactory;
	
	/**
	 * Builds default input pattern value array from pattern array.
	 * 
	 * @param array $pattern
	 * @return array
	 */
	public function buildDefaultInputPattern(array $pattern)
	{		
		$result = array(
			'pattern' => array(), 
			'firstDay' => null
		);
		
		$patternIterator = $this->patternIteratorFactory->create($pattern);		
		
		foreach($patternIterator as $week)
		{
			$result['pattern'][] = $week;
		}
		
		$firstDay = $this->calendarDateFactory->create()
			->floor(\Screwfix\CalendarDateTime::W)
			->format(\Screwfix\DateTime::FORMAT_DATE);
		
		$result['firstDay'] = $firstDay;
		
		return $result;
	}
	
	/**
	 * Adjusts patterns weeks order by given date of first day of pattern.
	 * Commonly pattern sent by client from signup or edit.
	 * 
	 * @param array $pattern
	 * @param string $date a date of first day in pattern. The format of date is yyyy-mm-dd
	 * @return array adjusted pattern
	 */
	public function adjustPattern(array $pattern, $date)
	{
		$adjustedPattern = array();
		
		$patternCount = count($pattern);
		
		$patternWeekNumber = $this->shiftPatternDateFactory->create($date)
			->week($patternCount);
		
		$startWeekNumber = ($patternWeekNumber === 0) ? 0 : $patternCount - $patternWeekNumber;
		
		$aroundIterator = $this->aroundIteratorFactory->create($pattern)
			->setStart($startWeekNumber);		
		
		foreach($aroundIterator as $week)
		{
			$adjustedPattern[] = $week;
		}
		
		return $adjustedPattern;
	}
	
	/**
	 * Helper method works out from fomrs employmentDate value whether it is date value or full.
	 * It returns processed value accordingly.
	 * eg. full is "s:full" of date is d:2014-01-01
	 * 
	 * @param \Nette\Utils\ArrayHash $formValues
	 * @return string
	 */
	public function workOutFormEmployment(\Nette\Utils\ArrayHash $formValues)
	{
		return isset($formValues->employmentDate)
			? 'd:' . $formValues->employmentDate
			: 's:full';
	}
}
