<?php

namespace Dan;

/**
 * Description of Lang
 *
 * @author Daniel Silovsky
 */
class Lang {
	
	/**
	 * eg. Lang::pluralize('%d year', '%d years, 3) // 3 years
	 *     Lang::pluralize('year', 'years, 3) // years
	 * 
	 * @param string $singular
	 * @param string $plural
	 * @param integer $number
	 * @return string
	 */
	static public function pluralize($singular, $plural, $number)
	{		
		return $number === 1 ? sprintf($singular, $number) : sprintf($plural, $number);
	}
}
