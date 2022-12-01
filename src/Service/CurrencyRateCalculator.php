<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CurrencyRateDTO;
use App\Retriever\CurrencyRatesRetrieverInterface;
use DateTime;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CurrencyRateCalculator implements CurrencyRateCalculatorInterface
{
    public function __construct(
        protected CacheInterface $cache,
        protected SerializerInterface $serializer,
        protected CurrencyRatesRetrieverInterface $retriever
    )
    {
    }

    public function getRate(DateTime $date, string $currency, string $baseCurrency): null|float|int
    {
        $key = sprintf('currency-rates-%s', $date->format('d-m-Y'));
        /** @psalm-suppress MixedAssignment */
        $rates = $this->cache->get($key, function () use ($date){
            // assume that daily rates are immutable and store cache forever
            $rates = $this->retriever->retrieve($date);

            return $this->serializer->serialize($rates, 'json');
        });

        /** @var CurrencyRateDTO[] $rates */
        $rates = $this->serializer->deserialize($rates, CurrencyRateDTO::class.'[]', 'json');

        $rate = $this->findRateByCurrency($rates, $currency);

        # Requested currency not found
        if (!$rate) {
            return null;
        }

        # Requested base currency is equal to stored one. Just return rate
        if ($rate->getBaseCurrency() === $baseCurrency) {
            return $rate->getRate();
        }

        # Requested base currency differs from stored one. Calculate rate manually
        $baseCurrencyRate = $this->findRateByCurrency($rates, $baseCurrency);
        $currencyRate = $this->findRateByCurrency($rates, $currency);
        if ($currencyRate && $baseCurrencyRate) {
            return round($currencyRate->getRate() / $baseCurrencyRate->getRate(), 4);
        }

        return null;
    }

    /**
     * @param CurrencyRateDTO[] $rates
     */
    protected function findRateByCurrency(array $rates, string $currency): ?CurrencyRateDTO
    {
        foreach ($rates as $rate) {
            if ($rate->getCurrency() === $currency) {
                return $rate;
            }
        }

        return null;
    }
}