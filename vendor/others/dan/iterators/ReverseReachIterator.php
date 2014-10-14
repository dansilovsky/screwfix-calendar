<?php

namespace Dan;

/**
 * Description of ReverseIterator
 *
 * @author Daniel Silovsky
 */
class ReverseReachIterator extends ReverseAssocIterator {
	
	protected $array = array();
	
	public function reachNext()
	{
		// all used method have opposite meaning than their name suggests. Appart from valid()
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
	
	public function reachPrev()
	{
		// all used method have opposite meaning than their name suggests. Appart from valid()
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
	
	public function reachNextKey()
	{
		// all used method have opposite meaning than their name suggests. Appart from valid()
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
	
	public function reachPrevKey()
	{
		// all used method have opposite meaning than their name suggests. Appart from valid()
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
}
