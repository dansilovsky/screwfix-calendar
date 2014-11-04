<?php

namespace Screwfix;

/**
 * Description of ShiftFacadeFactory
 *
 * @author Daniel Silovsky
 */
class ShiftFacadeFactory extends RepositoryFacadeFactory {
	
	
	public function __construct(ShiftRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}

	
	public function create()
	{
		$repository = $this->repositoryFactory->create();

		return new ShiftFacade($repository, $this->cache, $this->date);
	}

}