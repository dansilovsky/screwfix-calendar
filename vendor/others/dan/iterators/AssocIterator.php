<?php

namespace Dan;

/**
 * Description of AssocIterator
 *
 * @author Daniel Silovsky
 */
class AssocIterator implements \Iterator {
	
	/** @var array */
	protected $arr;
	
	protected $firstKey;
	
	protected $lastKey;


	public function __construct(array $arr = [])
	{
		$this->setArray($arr);
	}
	
	public function setArray(array $arr)
	{
		$this->arr = $arr;
		
		end($this->arr);
		$this->lastKey = key($this->arr);
		
		reset($this->arr);
		$this->firstKey = key($this->arr);
		
		return $this;
	}
	
	public function getArray()
	{
		return $this->arr;
	}
	
	/**
	 * @return mixed|false the value of current element or false if outside of pointer
	 */
	public function current()
	{
		return current($this->arr);
	}
	
	/**
	 * @return mixed|false the value of the last element or false for empty iterator.
	 */
	public function end()
	{
		return end($this->arr);
	}
	
	/**
	 * @return mixed|null the value of current key or null if outside of pointer or empty
	 */
	public function key()
	{
		return key($this->arr);
	}
	
	/**
	 * @return mixed|false
	 */
	public function next()
	{
		return next($this->arr);		
	}
	
	/**
	 * @return mixed|false
	 */
	public function prev()
	{
		return prev($this->arr);
	}
	
	/**
	 * @return mixed|false
	 */
	public function rewind()
	{
		return reset($this->arr);
	}
	
	/**
	 * @return boolean
	 */
	public function valid()
	{
		return key($this->arr) !== null;
	}
	
	/**
	 * Is current element first in iterator.
	 * 
	 * @return boolean 
	 */
	public function isFirst()
	{
		if ($this->firstKey === null)
		{
			return false;
		}
		
		return $this->firstKey === key($this->arr);
	}
	
	/**
	 * Is current element last in iterator.
	 * 
	 * @return boolean 
	 */
	public function isLast()
	{
		if ($this->lastKey === null)
		{
			return false;
		}
		
		return $this->lastKey === key($this->arr);
	}
}
