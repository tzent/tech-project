<?php

declare(strict_types=1);

namespace PS\Domain\Service\TransactionContext;

use PS\Domain\Entity\EntityInterface;
use PS\Domain\Entity\Transaction;
use PS\Domain\Service\Exchange;
use PS\Infrastructure\Repository\RepositoryInterface;

abstract class TransactionContextAbstract implements TransactionContextInterface
{
    /**
     * @var RepositoryInterface
     */
    protected RepositoryInterface $transactionRepository;

    /**
     * @var Exchange
     */
    protected Exchange $exchange;

    /**
     * @var array
     */
    protected array $config;

    /**
     * TransactionContextAbstract constructor.
     * @param RepositoryInterface $transactionRepository
     * @param Exchange $exchange
     * @param array $config
     */
    public function __construct(RepositoryInterface $transactionRepository, Exchange $exchange, array $config)
    {
        $this->transactionRepository = $transactionRepository;
        $this->exchange = $exchange;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function create(
        \DateTimeInterface $createdAt,
        int $userId,
        string $userType,
        string $transactionType,
        float $amount,
        string $currency
    ): EntityInterface {
        $transaction = (new Transaction())
            ->setCreatedAt($createdAt)
            ->setUserId($userId)
            ->setUserType($userType)
            ->setType($transactionType)
            ->setAmount($amount)
            ->setCurrency($currency);

        $transaction->setFee($this->calculateFee($transaction));

        return $transaction;
    }

    /**
     * {@inheritdoc}
     */
    protected function calculateFee(Transaction $transaction): float
    {
        $operation = lcfirst(str_replace('_', '', ucwords($transaction->getType(), '_')));

        return $this->{$operation}($transaction);
    }

    /**
     * @param string $userType
     * @param string $transactionType
     * @param float $amount
     * @param string $currency
     * @return float
     */
    protected function calculateFeeByType(
        string $userType,
        string $transactionType,
        float $amount,
        string $currency
    ): float {
        $commission = $this->config['commission'][$transactionType][$userType];
        $amount = $this->exchange->convert(
            (string) $amount,
            $currency,
            Exchange::DEFAULT_CURRENCY
        );

        $fee = bcdiv(bcmul($amount, (string) $commission['fee'], 3), '100', 3);
        $fee = $this->feeRange($fee, $commission);

        return (float) $this->exchange->convert($fee, Exchange::DEFAULT_CURRENCY, $currency);
    }

    /**
     * @param string $fee
     * @param array $commission
     * @return string
     */
    protected function feeRange(string $fee, array $commission): string
    {
        if ($commission['min'] > 0) {
            $commission['min'] = (string) $commission['min'];
            bccomp($fee, $commission['min'], 3) === -1 && $fee = $commission['min'];
        }

        if ($commission['max'] > 0) {
            $commission['max'] = (string) $commission['max'];
            bccomp($fee, $commission['max']) === 1 && $fee = $commission['max'];
        }

        return $fee;
    }

    /**
     * @param Transaction $transaction
     * @return float
     */
    abstract protected function cashIn(Transaction $transaction): float;

    /**
     * @param Transaction $transaction
     * @return float
     */
    abstract protected function cashOut(Transaction $transaction): float;
}
