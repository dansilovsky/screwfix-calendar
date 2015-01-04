<?php

namespace Screwfix;

/**
 * Description of SubshiftRepositoryFactory
 *
 * @author Daniel Silovsky
 */
class SubshiftRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new SubshiftRepository($this->context);
	}
}
