<?php

declare(strict_types=1);

namespace App\Domain\Service;

use PHPUnit\Framework\TestCase;
use PS\Domain\Service\Exchange;

class ExchangeTest extends TestCase
{
    /**
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param string $expected
     *
     * @dataProvider currenciesProvider
     */
    public function testConvert(float $amount, string $fromCurrency, string $toCurrency, string $expected): void
    {
        $exchange = new Exchange([
            'currencies' => [
                'default' => 'EUR',
                'rates' => [
                    'EUR' => 1,
                    'USD' => 1.1497,
                    'JPY' => 129.53,
                ],
            ],
        ]);
        $convertedAmount = $exchange->convert((string) $amount, $fromCurrency, $toCurrency);

        $this->assertIsString($convertedAmount);
        $this->assertEquals($expected, $convertedAmount);
    }

    /**
     * @return array
     */
    public function currenciesProvider(): array
    {
        return [
            'default currency to another' => [1, 'EUR', 'JPY', '129.530'],
            'other currency to default' => [129.53, 'JPY', 'EUR', '1.000'],
            'bigger amount' => [2, 'EUR', 'USD', '2.299'],
        ];
    }
}
