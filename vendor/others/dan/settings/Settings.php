<?php

namespace Dan;

/**
 * Settings
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class Settings extends \ArrayObject {

	protected $arr;
	
	protected $isArraySet = false;
	
	public function __construct(array $settings)
	{
		parent::__construct($settings, \ArrayObject::ARRAY_AS_PROPS);
	}
	
	public function get($path)
	{
		$indexes = explode('.', $path);
		
		$value = $this->getArrayCopy();		
		
		foreach ($indexes as $i)
		{
			if (isset($value[$i]))
			{
				$value = $value[$i];				
			}
			else
			{
				throw new Settings_UndefinedPathOrIndex_Exception;
			}
		}
		
		return $value;
	}


	public function offsetGet($index)
	{		
		$value = parent::offsetGet($index);
		
		return is_array($value) ? new self($value) : $value;
	}
	
	public function offsetSet($index, $newval)
	{
		throw new Settings_NotAllowedToSetAValue_Exeption;
	}
}
