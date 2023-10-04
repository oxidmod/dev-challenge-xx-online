<?php
declare(strict_types=1);

use App\Tests\Feature\TestCase as FeatureTestCase;
use App\Tests\TestCase as UnitTestCase;

uses(FeatureTestCase::class)
    ->group('feature')
    ->in('Feature');

uses(UnitTestCase::class)
    ->group('unit')
    ->in('Unit');