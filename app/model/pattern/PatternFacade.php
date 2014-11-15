<?php

namespace Screwfix;

/**
 * PatterFacade
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class PatternFacade extends RepositoryFacade {

	public function __construct(PatternRepository $repository, Cache $cache, CalendarDateTime $date)
	{
		parent::__construct($repository, $cache, $date);
	}
	
	/**
	 * @param integer $user_id
	 * @return \Nette\Database\Table\IRow|bool
	 */
	public function getByUserId($user_id)
	{
		return $this->repository->findByUserId($user_id)->fetch();
	}
	
	/**
	 * @param integer $user_id
	 * @return \Nette\Database\Table\IRow|null
	 */
	public function getCustomPatternByUserId($user_id)
	{
		$row = $this->getByUserId($user_id);
		
		return $row ? $row->ref('custom_pattern', 'custom_pattern_id') : null;
	}
	
	/**
	 * Returns assoc array (hash) build for template.
	 * Key is 'custom' 
	 * Value contain custom pattern in json string.
	 * 
	 * @param integer $user_id
	 * @return array|null
	 */
	public function getCustomPatternTemplateHash($user_id)
	{
		$customPatternRow = $this->getCustomPatternByUserId($user_id);
		
		if ($customPatternRow !== null)
		{
			$jsonPattern = unserialize($customPatternRow->pattern)->toJson();

			return ['0:0' => $jsonPattern];
		}
		
		return null;
	}

	/**
	 * Fetches a pattern for given user. 
	 * Unserializes the pattern and returns it.
	 * 
	 * @param  int  $user_id
	 * @return ShiftPatternFilter|false
	 */
	public function getPatternFilter($user_id)
	{
		$patternRow = $this->repository->findByUserId($user_id)->fetch();
		
		$refPatternRow = $patternRow->ref('sys_pattern', 'sys_pattern_id');
		
		if ($refPatternRow === null)
		{
			// sys pattern does not exist try to get custom pattern
			$refPatternRow = $patternRow->ref('custom_pattern', 'custom_pattern_id');
		}
		
		return $refPatternRow === null ? false : unserialize($refPatternRow->pattern);
	}
	
	public function save($userId, $sysPatternId, $customPatternId)
	{
		$data = array(
			'user_id' => $userId, 
			'sys_pattern_id' => $sysPatternId,
			'custom_pattern_id' => $customPatternId
		);
		
		return $this->repository->insert($data);
	}
	
	public function update($userId, ShiftPatternFilter $pattern)
	{
		$data = array(
			'pattern' => serialize($pattern)
		);
		
		$this->repository->findByUserId($userId)->update($data);
	}
	
	public function getUserPattern($user_id)
	{		
		return $this->repository->get($user_id);		
	}
}
