<?php

namespace Screwfix;

use Nette\Forms\Controls\SelectBox;

/**
 * Description of PatternSelectBox
 *
 * @author Daniel Silovsky
 */
class PatternSelectBox extends SelectBox {
	
	/** @var array */
	private $itemsComplete = array();
	
	public function __construct($label = null, array $items = null, array $itemsComplete = null)
	{
		parent::__construct($label, $items);
		
		if ($itemsComplete !== null) 
		{
			$this->itemsComplete = $itemsComplete;
		}		
	}
	
	/**
	 * Parent method returns only values that were added to control when initated. 
	 * But client sometimes dynamically adds a values.
	 * 
	 * @return scalar
	 */
	public function getValue()
	{		
		return array_key_exists($this->value, $this->itemsComplete) ? $this->value : 0;
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
