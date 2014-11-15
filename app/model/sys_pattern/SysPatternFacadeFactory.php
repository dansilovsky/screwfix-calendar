<?php

namespace Screwfix;

/**
 * Description of SysPatternFacadeFactory
 *
 * @author Daniel Silovsky
 */
class SysPatternFacadeFactory extends RepositoryFacadeFactory {
	
	public function __construct(SysPatternRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}
	
	public function create()
	{
		$repository = $this->repositoryFactory->create();		
		
		return new SysPatternFacade($repository, $this->cache, $this->date);
	}
	
}