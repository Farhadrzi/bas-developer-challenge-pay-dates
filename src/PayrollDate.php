<?php

declare(strict_types=1);

namespace PayrollCalculator;

use DateTime;

/**
 * Data Transfer Object for payroll date information
 */
readonly class PayrollDate
{
    public function __construct(
        public string $month,
        public DateTime $salaryDate,
        public DateTime $bonusDate
    ) {}
}