<?php

namespace Screwfix;

use Dan\ReverseReachIterator;
use Dan\Lang;

/**
 * Description of HolidayCredits
 *
 * @author Daniel Silovsky
 */
class HolidayCredits {

	/** @var array */
	private $credits;

	/** @var ReverseReachIterator **/
	private $reverseIterator;


	public function __construct(ReverseReachIterator $reverseIterator, Settings $settings)
	{
		$this->credits = $settings->get('holiday.credits')->getArrayCopy();
		$this->reverseIterator = $reverseIterator;
		$this->reverseIterator->setArray($this->credits);
	}

	public function getFormSelection()
	{
		$selection = [];

		foreach ($this->reverseIterator as $years => $credits)
		{
			if ($this->reverseIterator->isFirst())
			{
				$nextYears = $this->reverseIterator->reachNext();

				$selection[$years] = "Less than " . $this->pluralizeYear($nextYears);
				continue;
			}

			if ($this->reverseIterator->isLast())
			{
				$selection[$years] = "More than " . $this->pluralizeYear($years);
				continue;
			}

			$nextYears = $this->reverseIterator->reachNext();

			$selection[$years] = "Less than " . $this->pluralizeYear($nextYears) . " and more than " . $this->pluralizeYear($years);
		}

		return $selection;
	}

	private function pluralizeYear($number)
	{
		return Lang::pluralize('%d year', '%d years', $number);
	}
}
