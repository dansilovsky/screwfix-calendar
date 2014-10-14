<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use Dan\ReachIterator;
use Mockery as m;
use Tester\Assert;


$data = [
	'one' => 'first',
	'two' => 'second',
	'three' => 'third',
	'four' => 'fourth'
];
	
$obj = new ReachIterator($data);


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
	'second',
	'third',
	'fourth',
	false
];
Assert::same($expectedNext, $resultNext);

$expectedPrev = [
	false,
	'first',
	'second',
	'third'
];
Assert::same($expectedPrev, $resultPrev);

$expectedNextKey = [
	'two',
	'three',
	'four',
	null
];
Assert::same($expectedNextKey, $resultNextKey);

$expectedPrevKey = [
	null,
	'one',
	'two',
	'three'
];
Assert::same($expectedPrevKey, $resultPrevKey);

$expectedIsFirst = [
	true,
	false,
	false,
	false
];
Assert::same($expectedIsFirst, $resultIsFirst);

$expectedIsLast = [
	false,
	false,
	false,
	true
];
Assert::same($expectedIsLast, $resultIsLast);

// test with empty array
$data = [];
$obj = new ReachIterator($data);

Assert::same(false, $obj->reachNext());

Assert::same(false, $obj->reachPrev());

Assert::same(null, $obj->reachNextKey());

Assert::same(null, $obj->reachPrevKey());

Assert::same(false, $obj->isFirst());

Assert::same(false, $obj->isLast());

// test with one element in array
$data = [
	'one' => 'first'
];	
$obj = new ReachIterator($data);

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

$expectedNext = [false];
Assert::same($expectedNext, $resultNext);

$expectedPrev = [false];
Assert::same($expectedPrev, $resultPrev);

$expectedNextKey = [null];
Assert::same($expectedNextKey, $resultNextKey);

$expectedPrevKey = [null];
Assert::same($expectedPrevKey, $resultPrevKey);

$expectedIsFirst = [true];
Assert::same($expectedIsFirst, $resultIsFirst);

$expectedIsLast = [true];
Assert::same($expectedIsLast, $resultIsLast);

