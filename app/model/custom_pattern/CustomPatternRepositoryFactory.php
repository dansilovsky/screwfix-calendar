<?php

namespace Screwfix;

/**
 * Description of CustomPatternRepositoryFactory
 *
 * @author Daniel Silovsky
 */
class CustomPatternRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new CustomPatternRepository($this->context);
	}
}

