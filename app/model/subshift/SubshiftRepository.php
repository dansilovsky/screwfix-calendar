<?php

namespace Screwfix;

/**
 * Description of SubshiftRepository
 *
 * @author Daniel Silovsky
 */
class SubshiftRepository extends Repository {
	
	private $_name = 'subshift';
	
	public function __construct(\Nette\Database\Context $context)
	{
		parent::__construct($this->_name, $context);
	}
}