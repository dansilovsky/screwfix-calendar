<?php
namespace Tests;

use \Mockery as m;

class Helpers {
	
	/**
	 * Return traversable mock of selection which has one or more results (Nette\Database\Table\ActiverRow instances).
	 * At each iteration returns \Tests\ActiveRowMock.
	 * 
	 *   eg. Helpers::getRepositoryMock(['id', 'username'], [[1, 'dan'], [2, 'petr'], [3, 'bea']]);
	 * 
	 * @param array $keys array of keys in selection
	 * @param array $rows 
	 * @param string $repositoryClass optional
	 * @return \Mockery\CompositeExpectation
	 */
	static public function getRepositoryMock(array $keys, array $rows, $repositoryClass = 'Nette\Database\Table\Selection')
	{
		$timesCurrent = count($rows);
		$timesValid = $timesCurrent + 1;
		
		$returnValidValues = [];
		$returnCurrentValues = [];		
		
		foreach ($rows as $row)
		{
			$count = count($row);
			
			$activeRowArr = [];
			
			for($i = 0; $i < $count; $i++)
			{
				$activeRowArr[$keys[$i]] = $row[$i]; 
			}
			
			$returnCurrentValues[] = new ActiveRowMock($activeRowArr);
			
			$returnValidValues[] = true;
		}
		
		$returnValidValues[] = false;
		
		return m::mock($repositoryClass)
			->shouldReceive('rewind')
			->shouldReceive('valid')->times($timesValid)->andReturnValues($returnValidValues)
			->shouldReceive('current')->times($timesCurrent)->andReturnValues($returnCurrentValues)
			->shouldReceive('next')
			;
	}
}

class ActiveRowMock implements \ArrayAccess {	
	
	/** @var array */
	private $row;
	
	/** @var boolean */
	private $isInitialized = false;
	
	public function __construct(array $activeRowArr)
	{
		$this->row = $activeRowArr;
		
		foreach ($this->row as $key => $value)
		{
			$this->$key = $value;
		}
		
		$this->isInitialized = true;
	}
	
	public function __get($key)
	{
		if (array_key_exists($key, $this->row)) {
			return $this->row[$key];
		}
	}
	
	public function __set($key, $value)
	{
		if ($this->isInitialized)
		{
			throw new \Exception('ActiveRowMock is read-only');
		}
	}
	
	public function offsetExists($key)
	{
		return isset($this->row[$key]) ? true : false;
	}
	
	public function offsetSet($key, $value) 
	{
		throw new \Exception('ActiveRowMock is read-only');
	}
	
	public function offsetUnset($key)
	{
		throw new \Exception('ActiveRowMock is read-only');
	}
	
	public function offsetGet($key)
	{
		return $this->offsetExists($key) ? $this->row[$key] : null;
	}

}
