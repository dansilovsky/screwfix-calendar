<?php

namespace Screwfix;

use Nette\Utils\Json;

/**
 * BankHolidayFacade
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class BankHolidayFacade extends RepositoryFacade {

	public function __construct(BankHolidayRepository $repository, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repository, $cache, $date);
	}

	/**
	 * Builds array from \Nette\Database\Table\Selection adopted for use in BankHolidayFilter
	 *
	 * @param    string   $from
	 * @param    string   $to     
	 * @return   array          empty array if no holidays found between given dates
	 */
	public function bankHolidays(DateTime $from, DateTime $to)
	{
		$from = $from->format(Repository::FORMAT_DATE);
		$to = $to->format(Repository::FORMAT_DATE);
		
		$selection = $this->repository->between($from, $to);

		$holidays = array();

		foreach ($selection as $row)
		{
			// Nette database converts date type fields into \Nette\DateTime object
			$date = (string) $row->date;
			$date = substr($date, 0, 10);

			$holidays[$date] = $row->name;
		}
		
		return $holidays;
	}
	
	/**
	 * Adds bank holdays.
	 * Source is json file taken from GOV.UK api.
	 * If bank holiday already exists in the table then it is just ignored.
	 * It's not using BankHolidayRepository instance but context.
	 */
	public function addFromGovUk()
	{
		$jsonStrign = file_get_contents('https://www.gov.uk/bank-holidays.json');

		$uk = Json::decode($jsonStrign, Json::FORCE_ARRAY);
		
		$englishBankholidays = $uk['england-and-wales']['events'];
		
		foreach($englishBankholidays as $bankHoliday)
		{
			$date = $bankHoliday['date'];
			$name = $bankHoliday['title'];
			
			list($year, $month, $day) = explode('-', $date);
			
			if (!checkdate($month, $day, $year))
			{
				throw new BankHolidayFacade_InvalidData('Invalid date format.');
			}
			
			if (strlen($name) > 255)
			{
				throw new BankHolidayFacade_InvalidData('Name is too long.');
			}
			
			$data = [
				'date' => $date,
				'name' => $name
			];
			
			$this->context->query("INSERT IGNORE INTO `bank_holiday`", $data);
		}
	}
}
