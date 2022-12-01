<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\DTOSerializer;
use DateTime;
use App\DTO\CurrencyRateDTO;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DTOSerializerTest extends WebTestCase
{
    public function test_serializer(): void
    {
        $dtos = [
            new CurrencyRateDTO('X', 'RUR', 'USD', 60.45, new DateTime()),
            new CurrencyRateDTO('X', 'RUR', 'USD', 60.45, new DateTime()),
        ];
        $serializer = new DTOSerializer();
        $result = $serializer->serialize($dtos, 'json');

        self::assertNotEmpty($result);
        self::assertJson($result);

        $dtos = $serializer->deserialize($result, CurrencyRateDTO::class . '[]', 'json');

        self:;
        self::assertIsArray($dtos);
        self::assertCount(2, $dtos);
        foreach ($dtos as $dto) {
            self::assertInstanceOf(CurrencyRateDTO::class, $dto);
            self::assertEquals('X', $dto->getSource());
            self::assertEquals('RUR', $dto->getBaseCurrency());
            self::assertEquals('USD', $dto->getCurrency());
            self::assertEquals(60.45, $dto->getRate());
            self::assertInstanceOf(DateTime::class, $dto->getDate());
        }
    }
}