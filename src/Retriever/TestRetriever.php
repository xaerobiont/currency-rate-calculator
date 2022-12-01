<?php

declare(strict_types=1);

namespace App\Retriever;

use App\DTO\CurrencyRateDTO;
use DateTime;

final class TestRetriever implements CurrencyRatesRetrieverInterface
{
    public function retrieve(DateTime $date): array
    {
        return [
            new CurrencyRateDTO('TEST', 'RUR', 'USD', 60.5, $date),
            new CurrencyRateDTO('TEST', 'RUR', 'EUR', 58.4, $date),
        ];
    }
}