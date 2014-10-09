<?php

namespace Screwfix;

class PatternFacadeFactory extends RepositoryFacadeFactory {
	
	public function __construct(PatternRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}
	
}
