<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use Dan\ReverseReachIterator;
use Mockery as m;
use Tester\Assert;

// test with four elements array
$data = [
	'one' => 'first',
	'two' => 'second',
	'three' => 'third',
	'four' => 'fourth'
];
	
$obj = new ReverseReachIterator($data);


$resultNext = [];
$resultPrev = [];
$resultNextKey = [];
$resultPrevKey = [];
$resultIsFirst = [];
$resultIsLast = [];

foreach ($obj as $val)
{
	$resultNext[] = $obj->reachNext();
	$resultPrev[] = $obj->reachPrev();
	$resultNextKey[] = $obj->reachNextKey();
	$resultPrevKey[] = $obj->reachPrevKey();
	$resultIsFirst[] = $obj->isFirst();
	$resultIsLast[] = $obj->isLast();
	
}

$expectedNext = [
	false,
	'fourth',
	'third',
	'second',
];
Assert::same($expectedNext, $resultNext);

$expectedPrev = [
	'third',
	'second',
	'first',
	false
];
Assert::same($expectedPrev, $resultPrev);

$expectedNextKey = [
	null,
	'four',
	'three',
	'two'
];
Assert::same($expectedNextKey, $resultNextKey);

$expectedPrevKey = [
	'three',
	'two',
	'one',
	null
];
Assert::same($expectedPrevKey, $resultPrevKey);

$expectedIsFirst = [
	false,
	false,
	false,
	true
];
Assert::same($expectedIsFirst, $resultIsFirst);

$expectedIsLast = [
	true,
	false,
	false,
	false
];
Assert::same($expectedIsLast, $resultIsLast);

// test with empty array
$data = [];
$obj = new ReverseReachIterator($data);

Assert::same(false, $obj->reachNext());

Assert::same(false, $obj->reachPrev());

Assert::same(null, $obj->reachNextKey());

Assert::same(null, $obj->reachPrevKey());
