<?php

namespace Screwfix;

/**
 * Description of PatternRepositoryFactory
 *
 * @author Daniel Silovsky
 */
class PatternRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new PatternRepository($this->context);
	}
}
