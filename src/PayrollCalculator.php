<?php

declare(strict_types=1);

namespace PayrollCalculator;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Exception;

class PayrollCalculator
{
    /**
     * Calculate payroll dates for an entire year
     *
     * @param int $year
     * @return PayrollDate[]
     * @throws Exception
     */
    public function calculateForYear(int $year): array
    {
        $payrollDates = [];

        for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++) {
            // Skip months that have already passed in the current year
            if ($monthNumber < (int)date('n')) {
                continue;
            }

            // Get month name
            $date = Carbon::create($year, $monthNumber);
            $monthName = $date->format('F');

            $bonusDate = $this->calculateBonusDate($year, $monthNumber);
            $salaryDate = $this->calculateSalaryDate($year, $monthNumber);

            $payrollDates[] = new PayrollDate(
                month: $monthName,
                salaryDate: $salaryDate,
                bonusDate: $bonusDate
            );
        }

        return $payrollDates;
    }

    /**
     * Calculate salary payment date (last day of the month, or previous weekday if weekend)
     * @throws Exception
     */
    private function calculateSalaryDate(int $year, int $month): Carbon
    {
        $lastDay = Carbon::create($year, $month)->endOfMonth();
        return $lastDay->isWeekday() ? $lastDay : $lastDay->previousWeekday();
    }

    /**
     * Calculate bonus payment date (15th, or next Wednesday if weekend)
     * @throws Exception
     */
    private function calculateBonusDate(int $year, int $month): Carbon
    {
        $fifteenth = Carbon::create($year, $month,15)->addMonth();

        return $fifteenth->isWeekday() ? $fifteenth : $fifteenth->next(CarbonInterface::WEDNESDAY);
    }
}