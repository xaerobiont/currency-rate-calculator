<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\DTO\CurrencyRateDTO;
use App\Retriever\CurrencyRatesRetrieverInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RatesRetrieverTest extends WebTestCase
{
    /**
     * @group Integration
     */
    public function test()
    {
        /** @var CurrencyRatesRetrieverInterface $retriever */
        $retriever = static::getContainer()->get(CurrencyRatesRetrieverInterface::class);
        $result = $retriever->retrieve(new DateTime());
        foreach ($result as $item) {
            self::assertInstanceOf(CurrencyRateDTO::class, $item);
        }
    }
}