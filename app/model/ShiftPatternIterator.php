<?php

namespace Screwfix;

/**
 * PatternIterator iterates over the shift pattern from a shift pattern week. 
 * Pattern week is derived from given shift pattern date.
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class ShiftPatternIterator extends \Dan\Iterators\AroundIterator {

	protected $firstDay;

	public function __construct(array $pattern, ShiftPatternDate $shiftPatternDate)
	{
		parent::__construct($pattern);
		
		$this->setDate($shiftPatternDate);
	}
	
	public function setDate(ShiftPatternDate $shiftPatternDate)
	{
		$startPosition = $shiftPatternDate->week(count($this->array));
		
		$this->setStart($startPosition);
		
		return $this;
	}
}
