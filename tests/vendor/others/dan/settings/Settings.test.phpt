<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use \Mockery as m;
use Tester\Assert;


$settingsArr = array ( 
	'god' => array (
		'alian' => array ( 
			'tom' => 'Tom', 
		),
		'men' => array ( 
			'animal' => array (
				'horse' => array (
					'tom' => 'Tom', 
				),
			),
		), 
	), 
	'holiday' => array ( 
		'credits' => 33, 
		'yearStart' => '04-01', 
	),
	'master' => array (
		'slave' => array ( 
			'bea' => 'Bea', 
			'dan' => 'Dan', 
			'tom' => 'Tom', 
		), 
		'ted' => 'Ted', 
	),
);

$obj = new \Dan\Settings($settingsArr);


Assert::same('Tom', $obj->get('god.men.animal.horse.tom'));

Assert::exception(function() use ($obj) {       
    $obj->get('master.dumb');
}, 'Dan\Settings_UndefinedPathOrIndex_Exception');

Assert::exception(function() use ($obj) {       
    $obj->dumb = 'dumb';
}, 'Dan\Settings_NotAllowedToSetAValue_Exeption');

Assert::type('Dan\Settings', $obj->get('master'));


