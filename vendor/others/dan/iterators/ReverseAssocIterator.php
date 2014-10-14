<?php

namespace Dan;

/**
 * Description of ReverseAssocIterator
 *
 * @author Daniel Silovsky
 */
class ReverseAssocIterator extends AssocIterator {
	
	/**
	 * @return mixed|false 
	 */
	public function end()
	{
		return parent::rewind();
	}
	
	/**
	 * @return mixed|false
	 */
	public function next()
	{
		return parent::prev();		
	}
	
	/**
	 * @return mixed|false
	 */
	public function prev()
	{
		return parent::next();
	}
	
	/**
	 * @return mixed|false
	 */
	public function rewind()
	{
		return parent::end();
	}
	
	/**
	 * As opposed to method end(), this method returns true end of iterator and really moves pointer to the end of iterator
	 * @return mixed|false
	 */
	public function realEnd()
	{
		return parent::end();
	}
	
	/**
	 * As opposed to method rewind(), this method returns true beginning of iterator and really moves pointer to the beggining of iterator
	 * @return mixed|false
	 */
	public function realRewind()
	{
		return parent::rewind();
	}
}
