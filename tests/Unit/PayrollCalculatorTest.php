<?php

declare(strict_types=1);

namespace PayrollCalculator\Tests;

use Carbon\Carbon;
use PayrollCalculator\PayrollCalculator;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;

class PayrollCalculatorTest extends TestCase
{
    private PayrollCalculator $calculator;

    public function testCalculateForYearSkipsPastMonths(): void
    {
        // Mock current date as August 5, 2025
        Carbon::setTestNow(Carbon::create(2025, 8, 5));

        $payrollDates = $this->calculator->calculateForYear(2025);

        $this->assertCount(5, $payrollDates);
        $this->assertEquals('August', $payrollDates[0]->month);
        $this->assertEquals('December', $payrollDates[4]->month);
    }

    public function testCalculateSalaryDateLastDayWeekday(): void
    {
        // September 2024 ends on a Monday (2024-09-30)
        $salaryDate = $this->invokeMethod($this->calculator, 'calculateSalaryDate', [2024, 9]);
        $this->assertEquals('2024-09-30', $salaryDate->format('Y-m-d'));
    }

    public function testCalculateSalaryDateLastDayWeekend(): void
    {
        // August 2024 ends on a Saturday (2024-08-31), should use Friday (2024-08-30)
        $salaryDate = $this->invokeMethod($this->calculator, 'calculateSalaryDate', [2024, 8]);
        $this->assertEquals('2024-08-30', $salaryDate->format('Y-m-d'));
    }

    public function testCalculateBonusDateNextMonthWeekday(): void
    {
        // September 2024 -> October 15, 2024 is a Tuesday
        $bonusDate = $this->invokeMethod($this->calculator, 'calculateBonusDate', [2024, 9]);
        $this->assertEquals('2024-10-15', $bonusDate->format('Y-m-d'));
    }

    public function testCalculateBonusDateNextMonthWeekend(): void
    {
        // August 2024 -> September 15, 2024 is a Sunday, should use next Wednesday (2024-09-18)
        $bonusDate = $this->invokeMethod($this->calculator, 'calculateBonusDate', [2024, 8]);

        $this->assertEquals('2024-09-18', $bonusDate->format('Y-m-d'));
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

    protected function setUp(): void
    {
        $this->calculator = new PayrollCalculator();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
    }
}