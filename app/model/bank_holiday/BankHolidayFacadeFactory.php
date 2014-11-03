<?php

namespace Screwfix;

/**
 * Description of BankHolidayFacadeFactory
 *
 * @author Daniel Silovsky
 */
class BankHolidayFacadeFactory extends RepositoryFacadeFactory {
	
	public function __construct(BankHolidayRepositoryFactory $repositoryFactory, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repositoryFactory, $cache, $date);
	}
	
	public function create()
	{
		$repository = $this->repositoryFactory->create();		
		
		return new BankHolidayFacade($repository, $this->cache, $this->date);
	}	
}
