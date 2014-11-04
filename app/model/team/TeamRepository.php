<?php

namespace Screwfix;

/**
 * Description of TeamRepository
 *
 * @author Daniel Silovsky
 */
class TeamRepository extends Repository {
	
	private $_name = 'team';
	
	public function __construct(\Nette\Database\Context $context)
	{
		parent::__construct($this->_name, $context);
	}
}