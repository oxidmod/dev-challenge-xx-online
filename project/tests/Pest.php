<?php
declare(strict_types=1);

use App\Tests\TestCase;

uses(TestCase::class)
    ->group('feature')
    ->in('Feature');

uses(TestCase::class)
    ->group('unit')
    ->in('Unit');