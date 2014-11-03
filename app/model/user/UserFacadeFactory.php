<?php
namespace Screwfix;

/**
 * Description of UserFacadeFactory
 *
 * @author Daniel Silovsky
 */
class UserFacadeFactory extends RepositoryFacadeFactory {
	
	public function __construct(UserRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}
	
	public function create()
	{
		$repository = $this->repositoryFactory->create();		
		
		return new UserFacade($repository, $this->cache, $this->date);
	}
	
}
