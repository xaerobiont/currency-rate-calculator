<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use App\DTO\CurrencyRateDTO;
use App\Retriever\CurrencyRatesRetrieverInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class CurrencyRateCalculator implements CurrencyRateCalculatorInterface
{
    public function __construct(
        protected CacheInterface $cache,
        protected SerializerInterface $serializer,
        protected CurrencyRatesRetrieverInterface $retriever,
        #[Autowire('%currency.rates.cache.key%')]
        protected string $cacheKey,
        #[Autowire('%currency.rates.cache.ttl%')]
        protected ?int $cacheTTL = null,
    )
    {
    }

    public function getRate(DateTime $date, string $currency, string $baseCurrency): null|float|int
    {
        $key = sprintf($this->cacheKey, $date->format('d-m-Y'));
        /** @psalm-suppress MixedAssignment */
        $rates = $this->cache->get($key, function (CacheItem $item) use ($date) {
            $item->expiresAfter($this->cacheTTL);
            $rates = $this->retriever->retrieve($date);

            return $this->serializer->serialize($rates, 'json');
        });

        /** @var CurrencyRateDTO[] $rates */
        $rates = $this->serializer->deserialize($rates, CurrencyRateDTO::class . '[]', 'json');

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