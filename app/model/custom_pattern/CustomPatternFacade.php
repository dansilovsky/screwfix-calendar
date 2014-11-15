<?php

namespace Screwfix;

/**
 * Description of CustomPatternFacade
 *
 * @author Daniel Silovsky
 */
class CustomPatternFacade extends RepositoryFacade {

	public function __construct(CustomPatternRepository $repository, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repository, $cache, $date);
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
	
	public function save(ShiftPatternFilter $pattern)
	{
		$data = array(
			'pattern' => serialize($pattern)
		);
		
		return $this->repository->insert($data);
	}
}
