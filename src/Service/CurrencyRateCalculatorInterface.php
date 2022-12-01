<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;

interface CurrencyRateCalculatorInterface
{
    public function getRate(DateTime $date, string $currency, string $baseCurrency): null|float|int;
}