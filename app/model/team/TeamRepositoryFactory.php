<?php

namespace Screwfix;

/**
 * Description of TeamRepositoryFactory
 *
 * @author Daniel Silovsky
 */
class TeamRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new TeamRepository($this->context);
	}
}
