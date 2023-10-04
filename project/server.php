#!/usr/bin/env php
<?php
declare(strict_types=1);

$containerBuilder = require_once __DIR__ . '/container.php';
$container = $containerBuilder();

$container->get(\App\Application::class)->run();