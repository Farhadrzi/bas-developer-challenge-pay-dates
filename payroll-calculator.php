<?php

declare(strict_types=1);


use PayrollCalculator\Application;

require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = new Application();
    $exitCode = $app->run();
    exit($exitCode);
} catch (Exception $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . PHP_EOL);
    exit(1);
}