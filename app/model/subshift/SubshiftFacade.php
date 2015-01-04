<?php

namespace Screwfix;

use Nette\Utils\Json;

/**
 * Description of SubshiftFacade
 *
 * @author Daniel Silovsky
 */
class SubshiftFacade extends RepositoryFacade {

	public function __construct(SubshiftRepository $repository, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repository, $cache, $date);
	}
	
	/**
	 * @param integer $shiftId
	 * @return array
	 */
	public function getFormSelection($shiftId)
	{
		$this->repository->where('shift_id', $shiftId);
		
		return $this->getFormSelectionComplete();
	}
	
	/**
	 * @return array
	 */
	public function getFormSelectionComplete()
	{
		$selection = [];
		
		foreach ($this->repository as $row)
		{
			$selection[$row->id] = $row->title;
		}
		
		return $selection;
	}
	
	/**
	 * @return array
	 */
	public function getMap()
	{
		$map = [];
		
		$this->repository->order('shift_id');
		
		foreach($this->repository as $row)
		{
			if (!array_key_exists($row->shift_id, $map))
			{
				$map[$row->shift_id] = [];
			}
			
			$map[$row->shift_id][$row->id] = $row->title;
		}
		
		return $map;
	}
	
}
