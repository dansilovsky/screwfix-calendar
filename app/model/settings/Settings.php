<?php

namespace Screwfix;

/**
 * Settings
 *
 * @author Daniel Silovsky
 * @copyright (c) 2013, Daniel Silovsky
 * @license http://www.screwfix-calendar.co.uk/license
 */
class Settings extends \Dan\Settings {
	
	public function __construct(SettingsFacade $settingsFacade)
	{
		parent::__construct($settingsFacade->getSettings());
	}
}
