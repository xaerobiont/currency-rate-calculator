<?php

declare(strict_types=1);

namespace App\Retriever;

use App\DTO\CurrencyRateDTO;
use DateTime;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CBRRetriever implements CurrencyRatesRetrieverInterface
{
    const URL = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=%s';
    const BASE_CURRENCY = 'RUR';
    const SOURCE = 'CBR';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly ValidatorInterface $validator
    )
    {
    }

    /**
     * @return CurrencyRateDTO[]
     */
    public function retrieve(DateTime $date): array
    {
        $response = $this->client->request('GET', sprintf(self::URL, $date->format('d/m/Y')));
        if ($response->getStatusCode() !== 200) {
            throw new RetrieveException();
        }
        $response = simplexml_load_string($response->getContent());
        if (!$response || !property_exists($response, 'Valute')) {
            throw new RetrieveException();
        }

        $result = [];
        /**
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedPropertyFetch
         */
        foreach ($response->Valute as $item) {
            $rate = (float)str_replace(',', '.', (string)$item->Value);
            $rate /= (int)$item->Nominal;
            $rateDTO = new CurrencyRateDTO(
                self::SOURCE,
                self::BASE_CURRENCY,
                trim((string)$item->CharCode),
                round($rate, 4),
                $date
            );
            $errors = $this->validator->validate($rateDTO);
            if (count($errors)) {
                throw new ValidationFailedException('Got invalid currency rate', $errors);
            }
            $result[] = $rateDTO;
        }

        return $result;
    }
}