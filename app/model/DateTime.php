<?php

namespace Screwfix;

/**
 * DateTime
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class DateTime extends \Nette\Utils\DateTime {
	
	/**
	 * screwfix basic date format
	 */
	const FORMAT_DATE = 'Y-m-d';
	
	private $_oneDayInterval;
	
	private $_oneMonthInterval;
	
	private $_oneYearInterval;
	
	static private $_dayNames = array(0 => 'monday', 1 => 'tuesday', 2 => 'wednesday', 3 => 'thursday', 4 => 'friday', 5 => 'saturday', 6 => 'sunday');
	
	public function __construct($time = 'now', $object = null)
	{
		parent::__construct($time, $object);
		
		$this->_oneDayInterval = new \DateInterval('P1D');
		$this->_oneMonthInterval = new \DateInterval('P1M');
		$this->_oneYearInterval = new \DateInterval('P1Y');
		
	}
	
	/**
	 * Moves date to now.
	 * 
	 * @return DateTime
	 */
	public function toNow()
	{
		$this->modify('now');
		
		return $this;
	}
	
	/**
	 * Moves date to now on clone of this instance and returns the clone.
	 * 
	 * @return DateTime
	 */
	public function toNowClone()
	{
		$dolly = clone $this;
		$dolly->modify('now');
		
		return $dolly;
	}
	
	
	
	/**
	 * Ads an interval on the clone of this instance and returns the clone.
	 * 
	 * @param   \DateInterval   $interval
	 * @return  \Screwfix\DateTime
	 */
	public function addClone(\DateInterval $interval)
	{
		$dolly = clone $this;
		return $dolly->add($interval);
	}
	
	/**
	 * Subtracts an interval from the clone of this instance and returns the clone.
	 * 
	 * @param   \DateInterval   $interval
	 * @return  \Screwfix\DateTime
	 */
	public function subClone(\DateInterval $interval)
	{
		$dolly = clone $this;
		return $dolly->sub($interval);
	}
	
	/**
	 * Adds one day.
	 * 
	 * @return \Screwfix\DateTime
	 */
	public function addDay()
	{
		$this->add($this->_oneDayInterval);
		
		return $this;
	}
	
	/**
	 * Subtracts one day.
	 * 
	 * @return \Screwfix\DateTime
	 */
	public function subDay()
	{
		$this->sub($this->_oneDayInterval);
		
		return $this;
	}
	
	/**
	 * Adds one month.
	 * 
	 * @return \Screwfix\DateTime
	 */
	public function addMonth() 
	{
		$this->add($this->_oneMonthInterval);
		
		return $this;
	}
	
	/**
	 * Subtracts one month.
	 * 
	 * @return \Screwfix\DateTime
	 */
	public function subMonth() 
	{
		$this->sub($this->_oneMonthInterval);
		
		return $this;
	}
	
	/**
	 * Adds one year
	 * 
	 * @return \Screwfix\DateTime
	 */
	public function addYear()
	{
		$this->add($this->_oneYearInterval);
		
		return $this;
	}
	
	/**
	 * Subtracts one year
	 * 
	 * @return \Screwfix\DateTime
	 */
	public function subYear()
	{
		$this->sub($this->_oneYearInterval);
		
		return $this;
	}
	
	/**
	 * Returns a year
	 * 
	 * @return int
	 */
	public function getYear()
	{
		return (int) $this->format('Y');
	}	
	
	/**
	 * Returns a month
	 * 
	 * @return int
	 */
	public function getMonth()
	{
		return (int) $this->format('n');
	}
	
	/**
	 * Returns a day
	 * 
	 * @return int
	 */
	public function getDay()
	{
		return (int) $this->format('j');
	}
	
	/**
	 * 
	 * @param integer $day day number 0 is monday ... 6 is sunday
	 * @return string
	 */
	static public function dayName($day) 
	{
		return self::$_dayNames[$day];
	}
	
	/**
	 * Returns date string in format yyyy-mm-dd
	 * 
	 * @return string
	 */
	public function toString()
	{
		return $this->format(self::FORMAT_DATE);
	}
}
