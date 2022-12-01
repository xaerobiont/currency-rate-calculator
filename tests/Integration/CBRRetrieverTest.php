<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\DTO\CurrencyRateDTO;
use App\Retriever\CBRRetriever;
use App\Retriever\CurrencyRatesRetrieverInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CBRRetrieverTest extends WebTestCase
{
    /**
     * @group Integration
     */
    public function test_retrieve()
    {
        /** @var CurrencyRatesRetrieverInterface $retriever */
        $retriever = static::getContainer()->get(CBRRetriever::class);
        $result = $retriever->retrieve(new DateTime());
        foreach ($result as $item) {
            self::assertInstanceOf(CurrencyRateDTO::class, $item);
        }
    }
}