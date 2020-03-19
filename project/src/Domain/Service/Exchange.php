<?php

declare(strict_types=1);

namespace PS\Domain\Service;

class Exchange
{
    /**
     * @var mixed|string
     */
    private string $defaultCurrency;

    /**
     * @var array
     */
    private array $rates;

    public const CURRENCIES = ['USD', 'EUR', 'JPY'];
    public const DEFAULT_CURRENCY = 'EUR';

    /**
     * Exchange constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->defaultCurrency = $config['currencies']['default'];
        $this->rates = $config['currencies']['rates'];
    }

    public function convert(string $amount, string $fromCurrency, string $toCurrency): string
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        return $fromCurrency === $this->defaultCurrency
            ? bcmul($amount, $this->getRate($toCurrency), 3)
            : bcdiv($amount, $this->getRate($fromCurrency), 3);
    }

    /**
     * @param string $currency
     * @return string
     */
    public function getRate(string $currency): string
    {
        if (!in_array($currency, array_keys($this->rates), true)) {
            throw new \InvalidArgumentException(sprintf('Unsupported currency %s', $currency));
        }

        return (string) $this->rates[$currency];
    }

    /**
     * @return array
     */
    public function getSupportedCurrencies(): array
    {
        return array_keys($this->rates);
    }
}
