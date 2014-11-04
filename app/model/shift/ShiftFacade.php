<?php

namespace Screwfix;

/**
 * Description of ShiftFacade
 *
 * @author Daniel Silovsky
 */
class ShiftFacade extends RepositoryFacade {

	public function __construct(ShiftRepository $repository, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repository, $cache, $date);
	}	
	
	
	public function getFormSelection()
	{
		$selection = [];
		
		foreach ($this->repository as $row)
		{
			$selection[$row->id] = $row->title;
		}
		
		return $selection;
	}
	
}
