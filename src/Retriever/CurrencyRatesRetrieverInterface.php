<?php

declare(strict_types=1);

namespace App\Retriever;

use App\DTO\CurrencyRateDTO;
use DateTime;

interface CurrencyRatesRetrieverInterface
{
    /**
     * @return CurrencyRateDTO[]
     */
    public function retrieve(DateTime $date): array;
}