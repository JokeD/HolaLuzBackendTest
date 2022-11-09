<?php

declare(strict_types = 1);

namespace App\Application;


use App\Domain\SuspiciousReadingDetector;

class GetSuspiciousReadingsUseCase
{

    public function execute(array $readings): array
    {
        $suspiciousReadingDetector = new SuspiciousReadingDetector($readings);
        return $suspiciousReadingDetector->calculateMedianDeviationToPeriodsByCustomer();
    }
}