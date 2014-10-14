<?php

namespace Dan;
/**
 * Description of ReachIterator
 *
 * @author Daniel Silovsky
 */
class ReachIterator extends AssocIterator {	
	
	/**
	 * Reaches next element of iterator without changing pointer.
	 * 
	 * @return value of next element or false if there are no more elements
	 */
	public function reachNext()
	{
		$val = $this->next();
		
		if ($this->valid())
		{
			$this->prev();
		}
		else
		{
			$this->end();
		}
		
		return $val;
	}
	
	/**
	 * Reaches previous element of iterator without changing pointer.
	 * 
	 * @return value of previous element or false if there are no more elements
	 */
	public function reachPrev()
	{
		$val = $this->prev();
		
		if ($this->valid())
		{
			$this->next();
		}
		else 
		{
			$this->rewind();
		}
		
		return $val;
	}
	
	/**
	 * Reaches key of next elemen in iterator withou changing pointer.
	 * 
	 * @return key of next element or null if there are no more elements
	 */
	public function reachNextKey()
	{
		$this->next();
		
		$key = $this->key();
		
		if ($this->valid())
		{
			$this->prev();
		}
		else
		{
			$this->end();
		}
		
		return $key;			
	}
	
	/**
	 * Reaches key of previous elemen in iterator withou changing pointer.
	 * 
	 * @return key of previous element or null if there are no more elements
	 */
	public function reachPrevKey()
	{
		$this->prev();
		
		$key = $this->key();
		
		if ($this->valid())
		{
			$this->next();
		}
		else
		{
			$this->rewind();
		}
		
		return $key;
	}
}
