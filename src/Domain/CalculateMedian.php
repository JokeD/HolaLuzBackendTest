<?php

declare(strict_types = 1);

namespace App\Domain;


class CalculateMedian
{

    private int|float $median;

    private function __construct(int|float $median)
    {
        $this->median = $median;
    }

    public static function fromArrayOfReadings(array $customerReadings): self
    {
        usort($customerReadings, function ($first, $second) {
            return $first['reading'] <=> $second['reading'];
        });
        $middleIndex = count($customerReadings) / 2;
        if (is_float($middleIndex)) {
            return new self($customerReadings[(int)$middleIndex]['reading']);
        }
        $median = ($customerReadings[$middleIndex]['reading'] + $customerReadings[$middleIndex - 1]['reading']) / 2;
        return new self($median);
    }

    public function get(): int|float
    {
        return $this->median;
    }

}