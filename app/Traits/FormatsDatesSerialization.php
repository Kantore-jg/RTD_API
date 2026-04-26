<?php

namespace App\Traits;

use DateTimeInterface;

trait FormatsDatesSerialization
{
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }
}
