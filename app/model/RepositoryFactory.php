<?php

namespace Screwfix;

/**
 * Description of RepositoryFactory
 *
 * @author Daniel Silovsky
 */
abstract class RepositoryFactory {
	
	protected $context;
	
	public function __construct(\Nette\Database\Context $context)
	{
		$this->context = $context;
	}
	
	public function create()
	{
		return new self($this->context);
	}
}
