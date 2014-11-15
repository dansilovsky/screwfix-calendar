<?php

namespace Screwfix;

use Nette\Forms\Controls\SelectBox;

/**
 * Description of PatternSelectBox
 *
 * @author Daniel Silovsky
 */
class PatternSelectBox extends SelectBox {
	
	/**
	 * Parent method returns only values that were added to control when initated. 
	 * But client sometimes dynamically adds value 0.
	 * 
	 * @return scalar
	 */
	public function getValue()
	{
		if ($this->value === 0)
		{
			return $this->value;
		}
		
		return parent::getValue();
	}
	
	public function setValue($value)
	{
		
		parent::setValue($value);
	}
	
	public function loadHttpData()
	{		
		parent::loadHttpData();
	}
}
