<?php

namespace Screwfix;

/**
 * Description of CustomPatternFacadeFactory
 *
 * @author Daniel Silovsky
 */
class CustomPatternFacadeFactory extends RepositoryFacadeFactory {
	
	public function __construct(CustomPatternRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}
	
	public function create()
	{
		$repository = $this->repositoryFactory->create();		
		
		return new CustomPatternFacade($repository, $this->cache, $this->date);
	}
	
}