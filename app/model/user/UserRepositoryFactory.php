<?php

namespace Screwfix;

/**
 * Description of UserRepositoryFactory
 *
 * @author Daniel Silovsky
 */
class UserRepositoryFactory extends RepositoryFactory {
	
	public function create()
	{
		return new UserRepository($this->context);
	}
	
}
