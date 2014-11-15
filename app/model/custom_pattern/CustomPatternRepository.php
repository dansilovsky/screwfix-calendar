<?php

namespace Screwfix;

/**
 * Description of CustomPatternRepository
 *
 * @author Daniel Silovsky
 */
class CustomPatternRepository extends Repository {
	
	private $_name = 'custom_pattern';
	
	public function __construct(\Nette\Database\Context $context)
	{
		parent::__construct($this->_name, $context);
	}
}
