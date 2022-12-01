<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use DateTime;
use App\Service\CurrencyRateCalculatorInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RatesCalculatorTest extends WebTestCase
{
    private CurrencyRateCalculatorInterface $calculator;

    public function setUp(): void
    {
        parent::setUp();
        $this->calculator = static::getContainer()->get(CurrencyRateCalculatorInterface::class);
    }

    public function test_calculator(): void
    {
        $rate = $this->calculator->getRate(new DateTime('01-01-2007'), 'USD', 'RUR');
        self::assertIsNumeric($rate);
    }
}