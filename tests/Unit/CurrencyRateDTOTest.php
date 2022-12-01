<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use DateTime;
use App\DTO\CurrencyRateDTO;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CurrencyRateDTOTest extends WebTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function test_valid(): void
    {
        $dto = new CurrencyRateDTO('X', 'RUR', 'USD', 60.45, new DateTime());
        self::assertCount(0, $this->validator->validate($dto));
    }

    public function test_invalid(): void
    {
        $dto = new CurrencyRateDTO('X', 'RU', '', -60.45, new DateTime());
        self::assertCount(4, $this->validator->validate($dto));
    }
}