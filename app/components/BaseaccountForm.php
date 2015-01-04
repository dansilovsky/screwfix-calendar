<?php

namespace Screwfix;

/**
 * BaseaccountForm assigns common id.
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class BaseaccountForm extends \Nette\Application\UI\Form {
	
	private $_name = 'baseaccountForm';

	public function __construct(\Nette\ComponentModel\IContainer $parent = null, $name = null)
	{
		parent::__construct($parent, $name);
		
		$this->getElementPrototype()->id = 'frm-baseaccountForm';
	}
	
	public function getName()
	{
		return $this->_name;
	}
	
	public function addPatternSelect($name, $label = null, array $items = null, array $itemsComplete = null, $size = null)
	{		
		$control = new PatternSelectBox($label, $items, $itemsComplete);
		
		if ($size > 1) {
			$control->setAttribute('size', (int) $size);
		}
		
		return $this[$name] = $control;
	}
}
