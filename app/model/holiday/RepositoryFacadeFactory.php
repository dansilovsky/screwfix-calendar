<?php

namespace Screwfix;

/**
 * Description of RepositoryFacadeFactory
 *
 * @author Daniel Silovsky
 */
abstract class RepositoryFacadeFactory {
	
	protected $repositoryFactory;
	
	protected $cache;
	
	protected $date;
	
	public function __construct(RepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		$this->repositoryFactory = $repositoryFactory;
		$this->cache = $cache;
		$this->date = $date;
	}
}
