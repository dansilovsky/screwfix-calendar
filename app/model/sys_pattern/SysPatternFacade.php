<?php

namespace Screwfix;

/**
 * SysPatternFacade
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class SysPatternFacade extends RepositoryFacade {

	public function __construct(SysPatternRepository $repository, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repository, $cache, $date);
	}

	public function getPatterns()
	{
		return $this->repository;
	}

	/**
	 * @param  int  $id
	 * @return SysShiftPatternFilter|false
	 */
	public function getPatternFilter($id = null)
	{
		if ($id)
		{
			$patternRow = $this->repository->get($id);
		}
		else
		{
			$patternRow = $this->repository->first()->fetch();
		}
		
		return ($patternRow === false) ? false : unserialize($patternRow->pattern);
	}
	
	
	/**
	 * Get array of first unserialized shift pattern in the table
	 * 
	 * @return array|null
	 */
	public function getDefaultFormPattern()
	{		
		$patternRow = $this->repository->first()->fetch();
		
		if ($patternRow !== null)
		{
			$shiftPattern = unserialize($patternRow->pattern);
			
			return $shiftPattern->getArray();
		}
		
		return null;	
	}
	
	/**
	 * Get array of unserialized shift pattern for given $id.
	 * 
	 * @param integer $id
	 * @return array|null
	 */
	public function getFormPattern($id)
	{
		$patternRow = $this->repository->where('id', $id)->fetch();
		
		if ($patternRow !== null)
		{
			$shiftPattern = unserialize($patternRow->pattern);
			
			return $shiftPattern->getArray();
		}
		
		return null;
	}
	
	/**
	 * Returns assoc array (hash) build for template.
	 * Keys are made up of team_id . ':' shift_id. 
	 * Values contain pattern arrays.
	 * 
	 * @return array
	 */
	public function getMap()
	{
		$hash = [];
		$id;
		
		foreach ($this->repository as $row)
		{
			$id =  $row->shift_id . ':';
			$id .= $row->subshift_id !== null ? $row->subshift_id : 0;
			
			$patternArr = unserialize($row->pattern)->getArray();
			
			$hash[$id] = $patternArr;			
		}
		
		return $hash;
	}
	
	/**
	 * Only for temporary usage when you really need update don't be scared to change this method to suit your needs
	 * @param type $id
	 * @param \Screwfix\ShiftPatternFilter $pattern
	 */
	public function updatePattern($id, ShiftPatternFilter $pattern)
	{
		$pattern = serialize($pattern);
		
		$this->repository->get($id)->update(array('pattern' => $pattern));
	}
	
	/**
	 * Get id from team id and shift id
	 * 
	 * @param integer $shiftId
	 * @param integer $subshiftId
	 * @return integer|false if there is no row
	 */
	public function getId($shiftId, $subshiftId)
	{	
		if ($subshiftId == 0)
		{
			$subshiftId = null;
		}
					
		$row = $this->repository->where('shift_id', $shiftId)
		->where('subshift_id', $subshiftId)
		->fetch();
		
		return $row !== false ? (int) $row->id : false;
	}
	
}
