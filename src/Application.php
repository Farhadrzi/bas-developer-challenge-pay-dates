<?php

declare(strict_types=1);

namespace PayrollCalculator;

use Carbon\Carbon;
use Exception;
use InvalidArgumentException;
class Application
{
    private readonly PayrollCalculator $payrollCalculator;
    private readonly CsvWriter $csvWriter;

    public function __construct()
    {
        $this->payrollCalculator = new PayrollCalculator();
        $this->csvWriter = new CsvWriter();
    }

    public function run(): int
    {
        try {
            $config = getopt(
                'o:h',
                ['output:', 'help']
            );

            if ($config === false) {
                throw new InvalidArgumentException('Failed to parse command line arguments');
            }

            if (isset($config['help']) || isset($config['h'])) {
                $this->showHelp();
                return 0;
            }

            $output = $config['o'] ?? $config['output'] ?? null;

            $this->validateConfiguration($config);

            $payrollData = $this->payrollCalculator->calculateForYear(Carbon::now()->year);


            $this->csvWriter->write($output, $payrollData);

            echo "Successfully generated payroll dates in: {$output}" . PHP_EOL;

            return 0;

        } catch (InvalidArgumentException $e) {
            fwrite(STDERR, "Invalid argument: " . $e->getMessage() . PHP_EOL);
            $this->showUsage();
            return 1;
        } catch (Exception $e) {
            fwrite(STDERR, "Error: " . $e->getMessage() . PHP_EOL);
            return 1;
        }
    }

    private function showHelp(): void
    {
        echo "Payroll Calculator - Calculate salary and bonus payment dates" . PHP_EOL;
        echo PHP_EOL;
        echo "USAGE:" . PHP_EOL;
        echo "  php payroll-calculator.php -o output.csv" . PHP_EOL;
        echo "  php payroll-calculator.php --output=output.csv" . PHP_EOL;
        echo PHP_EOL;
        echo "OPTIONS:" . PHP_EOL;
        echo "  -o, --output=FILE     Output CSV file (required)" . PHP_EOL;
        echo "  -h, --help            Show this help message" . PHP_EOL;
        echo PHP_EOL;
        echo "EXAMPLES:" . PHP_EOL;
        echo "  php payroll-calculator.php -o payroll.csv" . PHP_EOL;
        echo "  php payroll-calculator.php --output=dates.csv" . PHP_EOL;
    }

    private function validateConfiguration(array $config): void
    {
        $output = $config['o'] ?? $config['output'] ?? null;
        if (!$output) {
            throw new InvalidArgumentException("Output file is required");
        }

        if (!str_ends_with(strtolower($output), '.csv')) {
            throw new InvalidArgumentException("Output file must have .csv extension");
        }

        $outputDir = dirname($output);
        if (!is_writable($outputDir)) {
            throw new InvalidArgumentException("Output directory is not writable: $outputDir");
        }
    }

    private function showUsage(): void
    {
        echo "Usage: php payroll-calculator.php -o output.csv" . PHP_EOL;
        echo "Use --help for more information." . PHP_EOL;
    }
}