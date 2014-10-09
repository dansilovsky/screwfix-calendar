<?php

namespace Screwfix;

/**
 * Description of RepositoryFacadeFactory
 *
 * @author Daniel Silovsky
 */
class RepositoryFacadeFactory {
	
	protected $repositoryFactory;
	
	protected $cache;
	
	protected $date;
	
	public function __construct(RepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		$this->repositoryFactory = $repositoryFactory;
		$this->cache = $cache;
		$this->date = $date;
	}
	
	public function create()
	{
		$repository = $this->repositoryFactory->create();
		
		return new self($repository, $this->_cache, $this->_date);
	}
}
