<?php

namespace Screwfix;

/**
 * Description of SubshiftFacadeFactory
 *
 * @author Daniel Silovsky
 */
class SubshiftFacadeFactory extends RepositoryFacadeFactory {
	
	
	public function __construct(SubshiftRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}

	
	public function create()
	{
		$repository = $this->repositoryFactory->create();

		return new SubshiftFacade($repository, $this->cache, $this->date);
	}

}