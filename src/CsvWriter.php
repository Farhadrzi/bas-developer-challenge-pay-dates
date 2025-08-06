<?php

declare(strict_types=1);

namespace PayrollCalculator;

use Exception;

class CsvWriter
{
    private const CSV_HEADERS = ['Month', 'Salary Payment Date', 'Bonus Payment Date'];
    private const DATE_FORMAT = 'Y-m-d';

    /**
     * Write payroll data to the CSV file
     *
     * @param string $filename
     * @param PayrollDate[] $payrollData
     * @throws Exception
     */
    public function write(string $filename, array $payrollData): void
    {
        $handle = fopen($filename, 'w');

        if ($handle === false) {
            throw new Exception("Cannot create or write to file: $filename");
        }

        try {
            // Write CSV headers
            if (fputcsv($handle, self::CSV_HEADERS) === false) {
                throw new Exception("Failed to write CSV headers");
            }

            // Write payroll data
            foreach ($payrollData as $payrollDate) {
                $row = [
                    $payrollDate->month,
                    $payrollDate->salaryDate->format(self::DATE_FORMAT),
                    $payrollDate->bonusDate->format(self::DATE_FORMAT)
                ];

                if (fputcsv($handle, $row) === false) {
                    throw new Exception("Failed to write CSV row for month: {$payrollDate->month}");
                }
            }
        } finally {
            fclose($handle);
        }
    }
}