<?php

declare(strict_types=1);

namespace App\DTO;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

final class CurrencyRateDTO
{
    public function __construct(
        #[Assert\NotBlank]
        private readonly string $source,
        #[Assert\NotBlank]
        #[Assert\Length(3)]
        private readonly string $baseCurrency,
        #[Assert\NotBlank]
        #[Assert\Length(3)]
        private readonly string $currency,
        #[Assert\NotBlank]
        #[Assert\Positive]
        private readonly float $rate,
        private readonly DateTime $date,
    )
    {
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }
}