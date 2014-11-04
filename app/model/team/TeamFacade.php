<?php

namespace Screwfix;

/**
 * Description of TeamFacade
 *
 * @author Daniel Silovsky
 */
class TeamFacade extends RepositoryFacade {

	public function __construct(TeamRepository $repository, Cache $cache, CalendarDateTime $date)
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
