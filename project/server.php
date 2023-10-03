#!/usr/bin/env php
<?php
declare(strict_types=1);

$container = require_once __DIR__ . '/container.php';
$container->get(\App\Application::class)->run();