<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use App\Service\CurrencyRateCalculatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/** @psalm-suppress PropertyNotSetInConstructor */
class CurrencyRateController extends AbstractController
{
    public function __construct(private readonly CurrencyRateCalculatorInterface $calculator)
    {
    }

    #[Route('/rates/get-rate/{date}/{currency}/{baseCurrency?RUR}', name: 'app_get_rate', methods: ['GET'])]
    public function getRate(string $date, string $currency, string $baseCurrency): Response
    {
        $date = new DateTime($date);
        $dayBefore = clone $date;
        $dayBefore = $dayBefore->sub(new DateInterval('P1D'));
        $rate = $this->calculator->getRate($date, $currency, $baseCurrency);
        $previousRate = $this->calculator->getRate($dayBefore, $currency, $baseCurrency);

        if (is_null($rate) || is_null($previousRate)) {
            throw new NotFoundHttpException('Could not find currency');
        }

        return $this->json([
            'rate' => $rate,
            'daily_diff' => $rate - $previousRate,
        ]);
    }
}