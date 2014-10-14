<?php

$container = require __DIR__ . '/../../../../bootstrap.php';

use Dan\Lang;
use \Mockery as m;
use Tester\Assert;

Assert::same('year', Lang::pluralize('year', 'years', 1));
Assert::same('years', Lang::pluralize('year', 'years', 2));
Assert::same('1 year', Lang::pluralize('%d year', '%d years', 1));
Assert::same('4 years', Lang::pluralize('%d year', '%d years', 4));
Assert::same('years', Lang::pluralize('year', 'years', -4));
Assert::same('years', Lang::pluralize('year', 'years', 0));


