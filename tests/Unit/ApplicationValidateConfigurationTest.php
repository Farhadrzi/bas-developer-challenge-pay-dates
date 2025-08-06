<?php

declare(strict_types=1);

namespace PayrollCalculator\Tests\Unit;

use InvalidArgumentException;
use PayrollCalculator\Application;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

class ApplicationValidateConfigurationTest extends TestCase
{
    private Application $application;

    protected function setUp(): void
    {
        $this->application = new Application();
    }

    public function testValidateConfigurationMissingOutput(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Output file is required');

        $this->invokeMethod($this->application, 'validateConfiguration', [[]]);
    }

    public function testValidateConfigurationNonCsvExtension(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Output file must have .csv extension');

        $this->invokeMethod($this->application, 'validateConfiguration', [['output' => 'output.txt']]);
    }

    public function testValidateConfigurationUnwritableDirectory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Output directory is not writable/');

        $this->invokeMethod($this->application, 'validateConfiguration', [['output' => '/unwritable/directory/output.csv']]);
    }

    public function testValidateConfigurationValid(): void
    {
        $tempDir = sys_get_temp_dir();
        $this->assertTrue(is_writable($tempDir), 'Temporary directory must be writable for this test');

        $config = ['output' => $tempDir . '/output.csv'];
        $this->invokeMethod($this->application, 'validateConfiguration', [$config]);

        $this->assertTrue(true);
    }

    /**
     * @throws ReflectionException
     */
    private function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new ReflectionMethod($object, $methodName);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($object, $parameters);
    }
}