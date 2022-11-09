<?php

declare(strict_types = 1);

namespace App\Domain;


class SuspiciousReadingDetector
{
    private array $aPeriodReadings;

    private ?array $aSuspiciousReadings;

    private array $aPeriodsReadingsByCustomer = [];

    public function __construct(array $aPeriodReadings)
    {
        $this->aPeriodReadings = $aPeriodReadings;
        $this->processPeriodsByCustomer();
        $this->addMedianToPeriodsByCustomer();
    }

    public function calculateMedianDeviationToPeriodsByCustomer($percentageDeviation = 50): ?array
    {
        foreach ($this->aPeriodsReadingsByCustomer as $periodsReadings) {
            foreach ($periodsReadings as $periodReading) {
                if (isset($periodReading['reading'])) {
                    $percentage = $this->percentDiff($periodsReadings['median'], $periodReading['reading']);
                    if ($percentage > $percentageDeviation) {
                        $this->aSuspiciousReadings[] = [
                            'client'                        => $periodReading['client'],
                            'period'                        => $periodReading['period'],
                            'reading'                       => $periodReading['reading'],
                            'median'                        => $periodsReadings['median'],
                            'deviationFromMedianPercentage' => $periodReading['reading'] > $periodsReadings['median'] ?
                                $percentage : $percentage * -1,
                        ];
                    }
                }
            }
        }
        return $this->aSuspiciousReadings ?? null;
    }

    private function percentDiff($median, $reading): float|int
    {
        return round((abs($reading - $median) / $median) * 100,2);
    }

    private function addMedianToPeriodsByCustomer()
    {
        foreach ($this->aPeriodsReadingsByCustomer as $customer => $periodReadings) {

            $this->aPeriodsReadingsByCustomer[$customer]['median'] = CalculateMedian::fromArrayOfReadings($periodReadings)->get();
        }
    }

    private function processPeriodsByCustomer(): void
    {
        foreach ($this->aPeriodReadings as $aPeriodReading) {
            $this->aPeriodsReadingsByCustomer[$aPeriodReading['client']][] = $aPeriodReading;
        }
    }

}