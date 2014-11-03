<?php
namespace Screwfix;

use Nette\Forms\Form,
	Nette\Utils\Html,
	Dan\Lang;

/**
 * Description of DateInput
 *
 * @author Daniel Silovsky
 */
class EmploymentDateInput extends \Nette\Forms\Controls\BaseControl
{
	private $day, $month, $year;
	
	/** @var HolidayCredits */
	private $holidayCredits;


	public function __construct(HolidayCredits $holidayCredits)
	{		
		$this->holidayCredits = $holidayCredits;
		
		$years = Lang::pluralize('%d year', '%d years', $this->holidayCredits->getBorderYearsNumber());
		
		parent::__construct("If you have worked less than $years, we need a date of start of your employment");
		
		$this->addRule(__CLASS__ . '::validateGregorianDate', 'Date is invalid.');
		$this->addRule(__CLASS__ . '::validateDateOld', 'Date is too old.');
	}

	/**
	 * @param string $value date in format yyyy-mm-dd
	 */
	public function setValue($value)
	{	
		if ($value)
		{
			list($year, $month, $day) = explode('-', $value);
			$this->year = $year;
			$this->month = $month;
			$this->day = $day;
		}
		else
		{
			$this->day = $this->month = $this->year = null;
		}
		
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getValue()
	{
		if (self::validateDate($this))
		{
			return self::validateNull($this) ? null : "$this->year-$this->month-$this->day";
		}
		
		return null;
	}

	public function loadHttpData()
	{
		$this->day = $this->getHttpData(Form::DATA_LINE, '[day]');
		$this->month = $this->getHttpData(Form::DATA_LINE, '[month]');
		$this->year = $this->getHttpData(Form::DATA_LINE, '[year]');
	}

	/**
	 * Generates control's HTML element.
	 */
	public function getControl()
	{
		$name = $this->getHtmlName();
		return Html::el()
			->add(Html::el('input')->name($name . '[day]')->id($this->getHtmlId())->value($this->day)->type('text')->placeholder('dd'))
			->add(Html::el('input')->name($name . '[month]')->id($this->getHtmlId())->value($this->month)->type('text')->placeholder('mm'))			
			->add(Html::el('input')->name($name . '[year]')->value($this->year)->type('text')->placeholder('yyyy'));
	}

	/**
	 * Is valid Gregorian date
	 * @return bool
	 */
	public static function validateGregorianDate(\Nette\Forms\IControl $control)
	{
		if (self::validateNull($control))
		{
			return true;
		}
		
		if ($control->day === '' || $control->month === '' || $control->year === '')
		{
			return false;
		}
		
		return checkdate($control->month, $control->day, $control->year);
	}
	
	/** 
	 * @return bool 
	 */
	public static function validateDateOld(\Nette\Forms\IControl $control)
	{
		if (self::validateNull($control))
		{
			return true;
		}
		
		$date = "$control->year-$control->month-$control->day";
		
		$dateBorder = $control->holidayCredits->getBorderDate();
		
		return $date > $dateBorder;
	}
	
	public static function validateDate(\Nette\Forms\IControl $control)
	{
		return self::validateGregorianDate($control)
			&& self::validateDateOld($control);
	}
	
	public static function validateNull(\Nette\Forms\IControl $control)
	{
		return ($control->day ===  null && $control->month === null && $control->year === null);
	}
}
