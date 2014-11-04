<?php

namespace Screwfix;

/**
 * Description of TeamFacadeFactory
 *
 * @author Daniel Silovsky
 */
class TeamFacadeFactory extends RepositoryFacadeFactory {
	
	public function __construct(TeamRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}
	
	public function create()
	{
		$repository = $this->repositoryFactory->create();		
		
		return new TeamFacade($repository, $this->cache, $this->date);
	}
	
}