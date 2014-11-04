<?php

namespace Screwfix;

/**
 * Description of ShiftRepository
 *
 * @author Daniel Silovsky
 */
class ShiftRepository extends Repository {
	
	private $_name = 'shift';
	
	public function __construct(\Nette\Database\Context $context)
	{
		parent::__construct($this->_name, $context);
	}
}