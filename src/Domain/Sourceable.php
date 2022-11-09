<?php

declare(strict_types = 1);

namespace App\Domain;

interface Sourceable
{
    public function fetch(): string;
}