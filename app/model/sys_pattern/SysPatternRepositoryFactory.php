<?php

namespace Screwfix;

/**
 * Description of SysPatternRepositoryFactory
 *
 * @author Daniel Silovsky
 */
class SysPatternRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new SysPatternRepository($this->context);
	}
}