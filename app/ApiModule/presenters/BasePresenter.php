<?php

namespace ApiModule;
use Nette\Utils\Json;

/**
 * BasePresenter
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
abstract class BasePresenter extends \Screwfix\CalendarPresenter implements ApiPresenter {

	private $_jsonData;
	
	protected function startup() {
		parent::startup();
		
		$this->readJsonData();
	}
	
	/**
	 * Read json data sent by client.
	 */
	private function readJsonData() {
		$jsonData = file_get_contents('php://input');
		
		$this->_jsonData = Json::decode($jsonData, Json::FORCE_ARRAY);
	}
	
	/**
	 * Get decoded json sent by client
	 * @return array|null Returns the value encoded in json in appropriate PHP array. NULL is returned if the json cannot be decoded.
	 */
	public function getJson() {
		return $this->_jsonData;
	}
}
