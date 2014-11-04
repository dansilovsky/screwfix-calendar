<?php

namespace Screwfix;

/**
 * Description of ShiftRepositoryFactory
 *
 * @author Daniel Silovsky
 */
class ShiftRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new ShiftRepository($this->context);
	}
}
